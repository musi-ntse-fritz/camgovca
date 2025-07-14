<?php
/**
 * CamGovCA Admin Login
 * Secure admin authentication with 2FA support
 */

// Start secure session
require_once '../includes/security_functions.php';
setSecureSession();

// Include required files
require_once '../config/database.php';
require_once '../includes/2fa_verification.php';
require_once '../includes/audit_logger.php';
require_once '../includes/email_service.php';

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

$error = '';
$success = '';
$show2FAForm = false;
$showResetForm = false;
$login_attempts = 0;

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_password') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Veuillez saisir votre adresse email.';
    } else {
        try {
            $pdo = getDBConnection();
            $logger = getAuditLogger();
            
            // Check if user exists with admin privileges
            $stmt = $pdo->prepare("
                SELECT user_id, username, first_name, last_name, email 
                FROM users 
                WHERE email = ? AND user_type IN ('admin', 'super_admin') AND deleted_at IS NULL
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Generate reset token
                $token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
                
                // Store reset token
                $stmt = $pdo->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
                $stmt->execute([$user['user_id'], $token, $expires_at]);
                
                // Send reset email
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/../reset-password.php?token=" . $token;
                
                $emailService = new EmailService();
                $email_result = $emailService->sendPasswordResetEmail($user['email'], $user['first_name'], $reset_link);
                
                if ($email_result['success']) {
                    $success = 'Les instructions de réinitialisation du mot de passe ont été envoyées à votre adresse email.';
                    
                    // Log password reset request
                    $logger->logSecurityEvent('admin_password_reset_requested', [
                        'user_id' => $user['user_id'],
                        'email' => $email
                    ]);
                } else {
                    $error = 'Échec de l\'envoi de l\'email de réinitialisation. Veuillez réessayer.';
                }
            } else {
                // Don't reveal if email exists or not for security
                $success = 'Si l\'adresse email existe dans notre système, les instructions de réinitialisation ont été envoyées.';
            }
        } catch (Exception $e) {
            error_log("Admin password reset error: " . $e->getMessage());
            $error = 'Une erreur s\'est produite. Veuillez réessayer.';
        }
    }
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] !== 'reset_password')) {
    $pdo = getDBConnection();
    $logger = getAuditLogger();
    
    if (isset($_POST['login'])) {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $error = 'Veuillez saisir le nom d\'utilisateur et le mot de passe.';
        } else {
            try {
                // Get user with admin privileges
                $stmt = $pdo->prepare("
                    SELECT user_id, username, email, password_hash, user_type, 
                           failed_login_attempts, locked_until, two_factor_enabled
                    FROM users 
                    WHERE username = ? AND user_type IN ('admin', 'super_admin')
                ");
                $stmt->execute([$username]);
                $user = $stmt->fetch();
                
                if ($user) {
                    // Check if account is locked
                    if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
                        $error = 'Compte temporairement verrouillé. Veuillez réessayer plus tard.';
                        $logger->logSecurityEvent('admin_login_locked', [
                            'username' => $username,
                            'locked_until' => $user['locked_until']
                        ]);
                    } else {
                        // Verify password
                        if (password_verify($password, $user['password_hash'])) {
                            // Reset failed attempts
                            $stmt = $pdo->prepare("
                                UPDATE users 
                                SET failed_login_attempts = 0, locked_until = NULL 
                                WHERE user_id = ?
                            ");
                            $stmt->execute([$user['user_id']]);
                            
                            // Check if 2FA is required
                            $verification = get2FAVerification($pdo);
                            if ($verification->is2FARequired('login', $user['user_id'])) {
                                // Generate and send 2FA code
                                $result = $verification->generate2FACode('login', $user['user_id'], $user['email']);
                                
                                if ($result['success']) {
                                    // Store user info in session for 2FA verification
                                    $_SESSION['pending_admin_user'] = $user;
                                    $_SESSION['pending_admin_operation'] = 'login';
                                    $show2FAForm = true;
                                    $success = 'Code de vérification envoyé à votre email.';
                                    
                                    $logger->logAuthEvent('admin_2fa_code_sent', $username, true);
                                } else {
                                    $error = 'Erreur lors de l\'envoi du code de vérification: ' . $result['message'];
                                    $logger->logAuthEvent('admin_2fa_code_failed', $username, false);
                                }
                            } else {
                                // No 2FA required, complete login
                                completeAdminLogin($user, $logger);
                            }
                        } else {
                            // Invalid password
                            handleFailedLogin($user['user_id'], $user['failed_login_attempts'], $pdo, $logger, $username);
                        }
                    }
                } else {
                    $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
                    $logger->logAuthEvent('admin_login_failed', $username, false);
                }
                
            } catch (Exception $e) {
                error_log("Admin login error: " . $e->getMessage());
                $error = 'Une erreur s\'est produite lors de la connexion.';
            }
        }
    } elseif (isset($_POST['verify_2fa'])) {
        // Handle 2FA verification
        $code = trim($_POST['2fa_code'] ?? '');
        
        if (empty($code)) {
            $error = 'Veuillez saisir le code de vérification.';
        } else {
            $pending_user = $_SESSION['pending_admin_user'] ?? null;
            $pending_operation = $_SESSION['pending_admin_operation'] ?? '';
            
            if ($pending_user && $pending_operation === 'login') {
                $verification = get2FAVerification($pdo);
                $result = $verification->verify2FACode($code, 'login', $pending_user['user_id']);
                
                if ($result['success']) {
                    // Complete login
                    completeAdminLogin($pending_user, $logger);
                    
                    // Clear pending session data
                    unset($_SESSION['pending_admin_user']);
                    unset($_SESSION['pending_admin_operation']);
                } else {
                    $error = $result['message'];
                    $logger->logAuthEvent('admin_2fa_verification_failed', $pending_user['username'], false);
                }
            } else {
                $error = 'Session de vérification invalide.';
            }
        }
    }
}

/**
 * Complete admin login process
 */
function completeAdminLogin($user, $logger) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_user_id'] = $user['user_id'];
    $_SESSION['admin_username'] = $user['username'];
    $_SESSION['admin_email'] = $user['email'];
    $_SESSION['admin_user_type'] = $user['user_type'];
    $_SESSION['admin_login_time'] = time();
    
    // Log successful login
    $logger->logAuthEvent('admin_login_success', $user['username'], true);
    
    // Redirect to dashboard
    header('Location: dashboard.php');
    exit();
}

/**
 * Handle failed login attempts
 */
function handleFailedLogin($user_id, $current_attempts, $pdo, $logger, $username) {
    $new_attempts = $current_attempts + 1;
    $max_attempts = 5;
    $lockout_duration = 15; // minutes
    
    if ($new_attempts >= $max_attempts) {
        // Lock account
        $locked_until = date('Y-m-d H:i:s', strtotime("+$lockout_duration minutes"));
        $stmt = $pdo->prepare("
            UPDATE users 
            SET failed_login_attempts = ?, locked_until = ? 
            WHERE user_id = ?
        ");
        $stmt->execute([$new_attempts, $locked_until, $user_id]);
        
        $error = "Compte verrouillé pour $lockout_duration minutes après $max_attempts tentatives échouées.";
        $logger->logSecurityEvent('admin_account_locked', [
            'username' => $username,
            'attempts' => $new_attempts,
            'locked_until' => $locked_until
        ]);
    } else {
        // Update failed attempts
        $stmt = $pdo->prepare("
            UPDATE users 
            SET failed_login_attempts = ? 
            WHERE user_id = ?
        ");
        $stmt->execute([$new_attempts, $user_id]);
        
        $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
        $logger->logAuthEvent('admin_login_failed', $username, false);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA - Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 400px;
            width: 100%;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo i {
            font-size: 3rem;
            color: #667eea;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .2fa-form {
            display: none;
        }
        .2fa-form.show {
            display: block;
        }
        .forgot-password {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        .forgot-password:hover {
            color: #764ba2;
        }
        .back-to-login {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        .back-to-login:hover {
            color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <i class="fas fa-shield-alt"></i>
            <h3 class="mt-3">CamGovCA</h3>
            <p class="text-muted">Administration</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <!-- Login Form -->
        <form method="POST" id="loginForm" class="<?php echo $show2FAForm ? 'd-none' : ''; ?>">
            <div class="mb-3">
                <label for="username" class="form-label">
                    <i class="fas fa-user"></i> Nom d'utilisateur
                </label>
                <input type="text" class="form-control" id="username" name="username" 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> Mot de passe
                </label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <button type="submit" name="login" class="btn btn-primary w-100">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </button>
            
            <div class="text-center mt-3">
                <a href="#" class="forgot-password" onclick="showResetForm()">
                    <i class="fas fa-key"></i> Mot de passe oublié?
                </a>
            </div>
        </form>
        
        <!-- Password Reset Form -->
        <form method="POST" id="resetForm" class="d-none">
            <input type="hidden" name="action" value="reset_password">
            
            <div class="mb-3">
                <label for="reset-email" class="form-label">
                    <i class="fas fa-envelope"></i> Adresse email
                </label>
                <input type="email" class="form-control" id="reset-email" name="email" required>
                <div class="form-text">
                    <i class="fas fa-info-circle"></i> 
                    Entrez votre adresse email pour recevoir un lien de réinitialisation.
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-paper-plane"></i> Envoyer le lien de réinitialisation
            </button>
            
            <div class="text-center mt-3">
                <a href="#" class="back-to-login" onclick="showLoginForm()">
                    <i class="fas fa-arrow-left"></i> Retour à la connexion
                </a>
            </div>
        </form>
        
        <!-- 2FA Verification Form -->
        <form method="POST" id="2faForm" class="<?php echo $show2FAForm ? '' : 'd-none'; ?>">
            <div class="mb-3">
                <label for="2fa_code" class="form-label">
                    <i class="fas fa-key"></i> Code de vérification
                </label>
                <input type="text" class="form-control" id="2fa_code" name="2fa_code" 
                       placeholder="Entrez le code à 6 chiffres" maxlength="6" required>
                <div class="form-text">
                    <i class="fas fa-info-circle"></i> 
                    Un code de vérification a été envoyé à votre email.
                </div>
            </div>
            
            <button type="submit" name="verify_2fa" class="btn btn-primary w-100">
                <i class="fas fa-check"></i> Vérifier
            </button>
            
            <div class="text-center mt-3">
                <button type="button" class="btn btn-link" onclick="resendCode()">
                    <i class="fas fa-redo"></i> Renvoyer le code
                </button>
            </div>
        </form>
        
        <div class="text-center mt-4">
            <a href="../index.php" class="text-muted">
                <i class="fas fa-arrow-left"></i> Retour à l'accueil
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showResetForm() {
            document.getElementById('loginForm').classList.add('d-none');
            document.getElementById('resetForm').classList.remove('d-none');
            document.getElementById('2faForm').classList.add('d-none');
        }
        
        function showLoginForm() {
            document.getElementById('resetForm').classList.add('d-none');
            document.getElementById('loginForm').classList.remove('d-none');
            document.getElementById('2faForm').classList.add('d-none');
        }
        
        function resendCode() {
            // Implement resend code functionality
            alert('Fonctionnalité de renvoi de code à implémenter');
        }
        
        // Auto-focus on 2FA code input
        document.addEventListener('DOMContentLoaded', function() {
            const codeInput = document.getElementById('2fa_code');
            if (codeInput) {
                codeInput.focus();
            }
        });
    </script>
</body>
</html> 