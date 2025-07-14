<?php
/**
 * CamGovCA Login Step 1 - Username/Password Only
 * This page only handles initial authentication
 */

session_start();
require_once 'config/database.php';
require_once 'includes/audit_logger.php';
require_once 'includes/email_service.php';

// Clear any existing sessions
if (isset($_SESSION['admin_logged_in'])) {
    unset($_SESSION['admin_logged_in']);
}

$error_message = '';
$success_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password.';
    } else {
        try {
            $pdo = getDBConnection();
            $auditLogger = new AuditLogger($pdo);
            
            // Get user by username
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND deleted_at IS NULL");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Update last login attempt
                $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
                $stmt->execute([$user['user_id']]);
                
                // Generate OTP
                $otp = sprintf('%06d', mt_rand(100000, 999999));
                
                // Store user info and OTP in session
                $_SESSION['step1_user_id'] = $user['user_id'];
                $_SESSION['step1_username'] = $user['username'];
                $_SESSION['step1_email'] = $user['email'];
                $_SESSION['step1_user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['step1_user_type'] = $user['user_type'];
                $_SESSION['step1_otp'] = $otp;
                $_SESSION['step1_otp_expires'] = time() + 300; // 5 minutes
                
                // Send OTP email
                $emailService = new EmailService('resend');
                $email_result = $emailService->sendOTPEmail($user['email'], $otp, $user['first_name']);
                
                if ($email_result['success']) {
                    // Log OTP generation
                    $auditLogger->logSecurityEvent('otp_generated_step1', [
                        'user_id' => $user['user_id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'otp' => $otp
                    ]);
                    
                    $success_message = 'Login successful! Please check your email for the OTP code.';
                    
                    // Redirect to OTP verification page after 2 seconds
                    header("refresh:2;url=login_step2.php");
                } else {
                    $error_message = 'Login successful but failed to send OTP: ' . $email_result['message'];
                    // Clear session data
                    session_destroy();
                }
            } else {
                $error_message = 'Invalid username or password.';
                
                // Log failed login attempt
                if ($user) {
                    $auditLogger->logSecurityEvent('login_failed_step1', [
                        'user_id' => $user['user_id'],
                        'username' => $user['username'],
                        'reason' => 'Invalid password'
                    ]);
                }
            }
        } catch (Exception $e) {
            error_log("Login step1 error: " . $e->getMessage());
            $error_message = 'An error occurred during login. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Step 1 - CamGovCA</title>
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
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .login-body {
            padding: 30px;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
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
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .forgot-password {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        .forgot-password:hover {
            color: #764ba2;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
        }
        .step.active {
            background: #667eea;
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h3><i class="fas fa-shield-alt"></i> CamGovCA</h3>
            <p class="mb-0">Administrative Access</p>
        </div>
        
        <div class="login-body">
            <div class="step-indicator">
                <div class="step active">1</div>
                <div class="step">2</div>
            </div>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                    <br><br>
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Redirecting to OTP verification...</p>
                    </div>
                </div>
            <?php else: ?>
                <h5 class="text-center mb-4">Login Page</h5>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </div>
                    
                    <div class="text-center">
                        <a href="forgot_password.php" class="forgot-password">
                            <i class="fas fa-key"></i> Forgot Password?
                        </a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 