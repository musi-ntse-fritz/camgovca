<?php
/**
 * CamGovCA Login Step 2 - OTP Verification Only
 * This page only handles OTP verification
 */

session_start();
require_once 'config/database.php';
require_once 'includes/audit_logger.php';
require_once 'includes/email_service.php';

$error_message = '';
$success_message = '';

// Check if user has completed step 1
if (!isset($_SESSION['step1_user_id']) || !isset($_SESSION['step1_otp'])) {
    header('Location: login_step1.php');
    exit;
}

// Check if OTP has expired
if (isset($_SESSION['step1_otp_expires']) && time() > $_SESSION['step1_otp_expires']) {
    // Clear session and redirect to login
    session_destroy();
    header('Location: login_step1.php?error=otp_expired');
    exit;
}

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp_code = trim($_POST['otp_code'] ?? '');
    
    if (empty($otp_code)) {
        $error_message = 'Please enter the OTP code.';
    } else {
        try {
            $pdo = getDBConnection();
            $auditLogger = new AuditLogger($pdo);
            
            // Verify OTP
            if ($otp_code === $_SESSION['step1_otp']) {
                // OTP is correct - complete login
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user_id'] = $_SESSION['step1_user_id'];
                $_SESSION['admin_username'] = $_SESSION['step1_username'];
                $_SESSION['admin_email'] = $_SESSION['step1_email'];
                $_SESSION['admin_user_name'] = $_SESSION['step1_user_name'];
                $_SESSION['admin_user_type'] = $_SESSION['step1_user_type'];
                $_SESSION['admin_login_time'] = time();
                
                // Log successful login
                $auditLogger->logSecurityEvent('login_success_step2', [
                    'user_id' => $_SESSION['step1_user_id'],
                    'username' => $_SESSION['step1_username'],
                    'with_2fa' => true
                ]);
                
                // Clear step1 session data
                unset($_SESSION['step1_user_id']);
                unset($_SESSION['step1_username']);
                unset($_SESSION['step1_email']);
                unset($_SESSION['step1_user_name']);
                unset($_SESSION['step1_user_type']);
                unset($_SESSION['step1_otp']);
                unset($_SESSION['step1_otp_expires']);
                
                $success_message = 'Login successful! Redirecting to dashboard...';
                
                // Redirect to admin dashboard after 2 seconds
                header("refresh:2;url=admin/dashboard.php");
            } else {
                $error_message = 'Invalid OTP code. Please try again.';
                
                // Log failed OTP attempt
                $auditLogger->logSecurityEvent('otp_verification_failed_step2', [
                    'user_id' => $_SESSION['step1_user_id'],
                    'username' => $_SESSION['step1_username'],
                    'provided_otp' => $otp_code
                ]);
            }
        } catch (Exception $e) {
            error_log("OTP verification step2 error: " . $e->getMessage());
            $error_message = 'An error occurred during verification. Please try again.';
        }
    }
}

// Handle resend OTP
if (isset($_POST['resend_otp'])) {
    try {
        // Check if session data exists
        if (!isset($_SESSION['step1_email']) || !isset($_SESSION['step1_user_name'])) {
            $error_message = 'Session expired. Please login again.';
        } else {
            $emailService = new EmailService('resend');
            $otp = sprintf('%06d', mt_rand(100000, 999999));
            
            // Update OTP in session
            $_SESSION['step1_otp'] = $otp;
            $_SESSION['step1_otp_expires'] = time() + 300; // 5 minutes
            
            // Send new OTP email
            $email_result = $emailService->sendOTPEmail($_SESSION['step1_email'], $otp, $_SESSION['step1_user_name']);
            
            if ($email_result['success']) {
                $success_message = 'New OTP code has been sent to your email.';
                
                // Log OTP resend
                $auditLogger = new AuditLogger($pdo);
                $auditLogger->logSecurityEvent('otp_resent_step2', [
                    'user_id' => $_SESSION['step1_user_id'],
                    'username' => $_SESSION['step1_username'],
                    'email' => $_SESSION['step1_email'],
                    'new_otp' => $otp
                ]);
            } else {
                $error_message = 'Failed to send new OTP: ' . $email_result['message'];
                error_log("Resend OTP failed: " . $email_result['message']);
            }
        }
    } catch (Exception $e) {
        error_log("OTP resend step2 error: " . $e->getMessage());
        $error_message = 'An error occurred while resending OTP: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Step 2 - CamGovCA</title>
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
        .otp-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .otp-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .otp-body {
            padding: 30px;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
            text-align: center;
            font-size: 18px;
            letter-spacing: 5px;
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
        .otp-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }
        .countdown {
            color: #dc3545;
            font-weight: bold;
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
    <div class="otp-container">
        <div class="otp-header">
            <h3><i class="fas fa-shield-alt"></i> CamGovCA</h3>
            <p class="mb-0">Two-Factor Authentication</p>
        </div>
        
        <div class="otp-body">
            <div class="step-indicator">
                <div class="step completed">1</div>
                <div class="step active">2</div>
            </div>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                    <?php if (strpos($success_message, 'Login successful') !== false): ?>
                        <br><br>
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Redirecting to dashboard...</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="otp-info">
                <h5>🔐 Step 2: Verify Your Identity</h5>
                <p class="mb-2">We've sent a 6-digit code to:</p>
                <strong><?php echo htmlspecialchars($_SESSION['step1_email']); ?></strong>
                <p class="mb-0 mt-2">
                    <small class="text-muted">
                        Code expires in: <span class="countdown" id="countdown">5:00</span>
                    </small>
                </p>
                
                <?php 
                // Check if this email might be logged locally
                $config = include 'config/email_config.php';
                $allowed_test_emails = $config['resend']['allowed_test_emails'] ?? ['fritzntse@gmail.com'];
                if (!in_array($_SESSION['step1_email'], $allowed_test_emails)): 
                ?>
                <div class="alert alert-info mt-3" style="font-size: 12px;">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Note:</strong> If you don't receive the email, the OTP code may be logged locally. 
                    Ask an administrator to check the <a href="view_email_log.php" target="_blank">Email Log Viewer</a>.
                </div>
                <?php endif; ?>
            </div>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="otp_code" class="form-label">Enter 6-digit code</label>
                    <input type="text" class="form-control" id="otp_code" name="otp_code" 
                           maxlength="6" pattern="[0-9]{6}" placeholder="000000" required autocomplete="off">
                    <div class="form-text">
                        <i class="fas fa-info-circle"></i> Enter the 6-digit code sent to your email
                    </div>
                </div>
                
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Verify
                    </button>
                </div>
            </form>
            
            <div class="text-center">
                <form method="POST" style="display: inline;">
                    <button type="submit" name="resend_otp" class="btn btn-link">
                        <i class="fas fa-redo"></i> Resend Code
                    </button>
                </form>
                <span class="mx-2">|</span>
                <a href="login_step1.php" class="back-to-login">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-focus on OTP input
        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.getElementById('otp_code');
            if (otpInput) {
                otpInput.focus();
            }
            
            // Countdown timer
            let timeLeft = 300; // 5 minutes in seconds
            const countdownElement = document.getElementById('countdown');
            
            const countdown = setInterval(function() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                countdownElement.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
                
                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    countdownElement.textContent = 'Expired';
                    countdownElement.style.color = '#dc3545';
                }
                timeLeft--;
            }, 1000);
            
            // Auto-submit when 6 digits are entered
            otpInput.addEventListener('input', function() {
                if (this.value.length === 6) {
                    this.form.submit();
                }
            });
        });
    </script>
</body>
</html> 