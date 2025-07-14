<?php
/**
 * Forgot Password Page
 * Handles password reset requests
 */

session_start();
require_once 'config/database.php';
require_once 'includes/audit_logger.php';
require_once 'includes/email_service.php';

$error_message = '';
$success_message = '';

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error_message = 'Please enter your email address.';
    } else {
        try {
            $pdo = getDBConnection();
            $auditLogger = new AuditLogger($pdo);
            
            // Check if user exists
            $stmt = $pdo->prepare("SELECT user_id, username, first_name, last_name, email FROM users WHERE email = ? AND deleted_at IS NULL");
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
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/reset-password.php?token=" . $token;
                
                $emailService = new EmailService();
                $email_result = $emailService->sendPasswordResetEmail($user['email'], $user['first_name'], $reset_link);
                
                if ($email_result['success']) {
                    $success_message = 'Password reset instructions have been sent to your email address.';
                    
                    // Log password reset request
                    $auditLogger->logSecurityEvent('password_reset_requested', [
                        'user_id' => $user['user_id'],
                        'email' => $email
                    ]);
                } else {
                    $error_message = 'Failed to send reset email. Please try again.';
                }
            } else {
                // Don't reveal if email exists or not for security
                $success_message = 'If the email address exists in our system, password reset instructions have been sent.';
            }
        } catch (Exception $e) {
            error_log("Password reset error: " . $e->getMessage());
            $error_message = 'An error occurred. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - CamGovCA</title>
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
        .forgot-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .forgot-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .forgot-body {
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
    <div class="forgot-container">
        <div class="forgot-header">
            <h3><i class="fas fa-key"></i> CamGovCA</h3>
            <p class="mb-0">Reset Your Password</p>
        </div>
        
        <div class="forgot-body">
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                    <br><br>
                    <a href="login_step1.php" class="btn btn-success">
                        <i class="fas fa-sign-in-alt"></i> Back to Login
                    </a>
                </div>
            <?php else: ?>
                <h5 class="text-center mb-4">Forgot Your Password?</h5>
                <p class="text-muted text-center mb-4">
                    Enter your email address and we'll send you a link to reset your password.
                </p>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Reset Link
                        </button>
                    </div>
                    
                    <div class="text-center">
                        <a href="login_step1.php" class="back-to-login">
                            <i class="fas fa-arrow-left"></i> Back to Login
                        </a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 