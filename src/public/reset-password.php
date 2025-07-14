<?php
/**
 * Password Reset Page
 * Handles password reset via email token
 */

session_start();
require_once 'config/database.php';
require_once 'includes/audit_logger.php';

$error_message = '';
$success_message = '';
$token_valid = false;
$user = null;

// Get token from URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $error_message = 'Invalid reset link. Please request a new password reset.';
} else {
    try {
        $pdo = getDBConnection();
        
        // Check if token exists and is valid
        $stmt = $pdo->prepare("
            SELECT prt.*, u.username, u.first_name, u.last_name, u.email 
            FROM password_reset_tokens prt 
            JOIN users u ON prt.user_id = u.user_id 
            WHERE prt.token = ? AND prt.expires_at > NOW() AND prt.used_at IS NULL 
            AND u.deleted_at IS NULL
        ");
        $stmt->execute([$token]);
        $token_data = $stmt->fetch();
        
        if ($token_data) {
            $token_valid = true;
            $user = $token_data;
        } else {
            $error_message = 'Reset link has expired or is invalid. Please request a new password reset.';
        }
    } catch (Exception $e) {
        error_log("Password reset token validation error: " . $e->getMessage());
        $error_message = 'An error occurred. Please try again.';
    }
}

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token_valid) {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($password) || empty($confirm_password)) {
        $error_message = 'Please enter both password and confirmation.';
    } elseif (strlen($password) < 8) {
        $error_message = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } else {
        try {
            $pdo = getDBConnection();
            $auditLogger = new AuditLogger($pdo);
            
            // Update user password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
            $stmt->execute([$password_hash, $user['user_id']]);
            
            // Mark token as used
            $stmt = $pdo->prepare("UPDATE password_reset_tokens SET used_at = NOW() WHERE token = ?");
            $stmt->execute([$token]);
            
            // Log password reset
            $auditLogger->logSecurityEvent('password_reset_completed', [
                'user_id' => $user['user_id'],
                'username' => $user['username']
            ]);
            
            $success_message = 'Password has been reset successfully. You can now login with your new password.';
            $token_valid = false; // Hide the form after successful reset
            
        } catch (Exception $e) {
            error_log("Password reset error: " . $e->getMessage());
            $error_message = 'An error occurred while resetting your password. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - CamGovCA</title>
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
        .reset-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
        }
        .reset-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .reset-body {
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
        .password-strength {
            margin-top: 5px;
            font-size: 12px;
        }
        .strength-weak { color: #dc3545; }
        .strength-medium { color: #ffc107; }
        .strength-strong { color: #28a745; }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <h3><i class="fas fa-key"></i> Reset Password</h3>
            <p class="mb-0">CamGovCA Administrative Access</p>
        </div>
        
        <div class="reset-body">
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
                        <i class="fas fa-sign-in-alt"></i> Go to Login
                    </a>
                </div>
            <?php endif; ?>
            
            <?php if ($token_valid && $user): ?>
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle"></i> 
                    Reset password for user: <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                </div>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required 
                                   minlength="8" onkeyup="checkPasswordStrength()">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="password-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="password-strength"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required 
                                   minlength="8" onkeyup="checkPasswordMatch()">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye" id="confirm-password-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="password-match"></div>
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Reset Password
                        </button>
                    </div>
                    
                    <div class="text-center">
                        <a href="login_step1.php" class="back-to-login">
                            <i class="fas fa-arrow-left"></i> Back to Login
                        </a>
                    </div>
                </form>
            <?php elseif (empty($success_message)): ?>
                <div class="text-center">
                    <p>This reset link is invalid or has expired.</p>
                    <a href="login_step1.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Login
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById(fieldId === 'password' ? 'password-eye' : 'confirm-password-eye');
            
            if (field.type === 'password') {
                field.type = 'text';
                eye.className = 'fas fa-eye-slash';
            } else {
                field.type = 'password';
                eye.className = 'fas fa-eye';
            }
        }
        
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthDiv = document.getElementById('password-strength');
            
            let strength = 0;
            let feedback = '';
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            if (strength < 3) {
                feedback = '<span class="strength-weak">Weak password</span>';
            } else if (strength < 5) {
                feedback = '<span class="strength-medium">Medium strength password</span>';
            } else {
                feedback = '<span class="strength-strong">Strong password</span>';
            }
            
            strengthDiv.innerHTML = feedback;
        }
        
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchDiv = document.getElementById('password-match');
            
            if (confirmPassword === '') {
                matchDiv.innerHTML = '';
            } else if (password === confirmPassword) {
                matchDiv.innerHTML = '<span class="strength-strong">Passwords match</span>';
            } else {
                matchDiv.innerHTML = '<span class="strength-weak">Passwords do not match</span>';
            }
        }
    </script>
</body>
</html> 