<?php
/**
 * Admin 2FA Settings Management
 * Interface for managing Two-Factor Authentication settings
 */

session_start();
require_once '../config/database.php';
require_once '../includes/2fa_manager.php';
require_once '../includes/audit_logger.php';

// Check admin authentication
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit;
}

$pdo = getDBConnection();
$twoFactorAuth = new TwoFactorAuth($pdo);
$auditLogger = new AuditLogger($pdo);

$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'enable_2fa':
            $user_id = $_POST['user_id'] ?? 0;
            $method = $_POST['method'] ?? 'email';
            
            if ($user_id > 0) {
                $result = $twoFactorAuth->enable2FA($user_id, $method);
                
                if ($result['success']) {
                    $message = "2FA enabled successfully for user ID {$user_id}. Backup codes generated.";
                    $message_type = 'success';
                    
                    // Log admin action
                    logAdminAction('2fa_enabled', 'users', [
                        'target_user_id' => $user_id,
                        'method' => $method
                    ]);
                } else {
                    $message = "Failed to enable 2FA: " . $result['message'];
                    $message_type = 'error';
                }
            }
            break;
            
        case 'disable_2fa':
            $user_id = $_POST['user_id'] ?? 0;
            
            if ($user_id > 0) {
                $result = $twoFactorAuth->disable2FA($user_id);
                
                if ($result['success']) {
                    $message = "2FA disabled successfully for user ID {$user_id}.";
                    $message_type = 'success';
                    
                    // Log admin action
                    logAdminAction('2fa_disabled', 'users', [
                        'target_user_id' => $user_id
                    ]);
                } else {
                    $message = "Failed to disable 2FA: " . $result['message'];
                    $message_type = 'error';
                }
            }
            break;
            
        case 'generate_backup_codes':
            $user_id = $_POST['user_id'] ?? 0;
            
            if ($user_id > 0) {
                $result = $twoFactorAuth->enable2FA($user_id, 'email'); // Re-enable to get new codes
                
                if ($result['success']) {
                    $message = "New backup codes generated for user ID {$user_id}.";
                    $message_type = 'success';
                    
                    // Log admin action
                    logAdminAction('backup_codes_regenerated', 'users', [
                        'target_user_id' => $user_id
                    ]);
                } else {
                    $message = "Failed to generate backup codes: " . $result['message'];
                    $message_type = 'error';
                }
            }
            break;
            
        case 'send_test_otp':
            $user_id = $_POST['user_id'] ?? 0;
            
            if ($user_id > 0) {
                $otp_result = $twoFactorAuth->generateOTP($user_id, 'test');
                
                if ($otp_result['success']) {
                    $email_result = $twoFactorAuth->sendOTPEmail($user_id, $otp_result['otp']);
                    
                    if ($email_result['success']) {
                        $message = "Test OTP sent successfully to user ID {$user_id}. OTP: {$otp_result['otp']}";
                        $message_type = 'success';
                    } else {
                        $message = "OTP generated but email failed: " . $email_result['message'];
                        $message_type = 'warning';
                    }
                    
                    // Log admin action
                    logAdminAction('test_otp_sent', 'users', [
                        'target_user_id' => $user_id
                    ]);
                } else {
                    $message = "Failed to generate test OTP: " . $otp_result['message'];
                    $message_type = 'error';
                }
            }
            break;
    }
}

// Get users with 2FA status
try {
    $stmt = $pdo->prepare("
        SELECT user_id, username, email, first_name, last_name, 
               two_factor_enabled, two_factor_method, 
               created_at, last_login
        FROM users 
        ORDER BY user_id
    ");
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    $users = [];
    $message = "Error fetching users: " . $e->getMessage();
    $message_type = 'error';
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>2FA Settings - CamGovCA Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #667eea; color: white; padding: 15px; margin: -20px -20px 20px -20px; border-radius: 8px 8px 0 0; }
        .message { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; margin: 2px; }
        .btn-primary { background: #667eea; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-info { background: #17a2b8; color: white; }
        .btn:hover { opacity: 0.8; }
        .status-enabled { color: green; font-weight: bold; }
        .status-disabled { color: red; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; margin: 15% auto; padding: 20px; border-radius: 8px; width: 80%; max-width: 500px; }
        .close { float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .backup-codes { background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; }
        .nav { margin-bottom: 20px; }
        .nav a { color: #667eea; text-decoration: none; margin-right: 15px; }
        .nav a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Two-Factor Authentication Settings</h1>
            <p>Manage 2FA settings for CamGovCA users</p>
        </div>
        
        <div class="nav">
            <a href="dashboard.php">← Back to Dashboard</a>
            <a href="audit-logs.php">View Audit Logs</a>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="info">
            <h3>2FA Management Overview</h3>
            <p>This interface allows you to:</p>
            <ul>
                <li>Enable/disable 2FA for users</li>
                <li>Generate new backup codes</li>
                <li>Send test OTP codes</li>
                <li>Monitor 2FA status across all users</li>
            </ul>
        </div>
        
        <h2>User 2FA Status</h2>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>2FA Status</th>
                    <th>Method</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <?php if ($user['two_factor_enabled']): ?>
                                <span class="status-enabled">✓ Enabled</span>
                            <?php else: ?>
                                <span class="status-disabled">✗ Disabled</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($user['two_factor_method'] ?? 'N/A'); ?></td>
                        <td><?php echo $user['last_login'] ? htmlspecialchars($user['last_login']) : 'Never'; ?></td>
                        <td>
                            <?php if ($user['two_factor_enabled']): ?>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="action" value="disable_2fa">
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to disable 2FA for this user?')">Disable 2FA</button>
                                </form>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="action" value="generate_backup_codes">
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                    <button type="submit" class="btn btn-warning">New Backup Codes</button>
                                </form>
                            <?php else: ?>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="action" value="enable_2fa">
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                    <input type="hidden" name="method" value="email">
                                    <button type="submit" class="btn btn-primary">Enable 2FA</button>
                                </form>
                            <?php endif; ?>
                            
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="action" value="send_test_otp">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <button type="submit" class="btn btn-info">Send Test OTP</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="info">
            <h3>2FA Statistics</h3>
            <?php
            $enabled_count = 0;
            $disabled_count = 0;
            foreach ($users as $user) {
                if ($user['two_factor_enabled']) {
                    $enabled_count++;
                } else {
                    $disabled_count++;
                }
            }
            ?>
            <p><strong>Total Users:</strong> <?php echo count($users); ?></p>
            <p><strong>2FA Enabled:</strong> <?php echo $enabled_count; ?> (<?php echo round(($enabled_count / count($users)) * 100, 1); ?>%)</p>
            <p><strong>2FA Disabled:</strong> <?php echo $disabled_count; ?> (<?php echo round(($disabled_count / count($users)) * 100, 1); ?>%)</p>
        </div>
        
        <div class="info">
            <h3>Security Recommendations</h3>
            <ul>
                <li>Enable 2FA for all administrative accounts</li>
                <li>Regularly rotate backup codes</li>
                <li>Monitor failed 2FA attempts in audit logs</li>
                <li>Use email verification for critical operations</li>
                <li>Consider implementing SMS 2FA for high-security environments</li>
            </ul>
        </div>
    </div>
    
    <script>
        // Add confirmation for destructive actions
        function confirmAction(message) {
            return confirm(message);
        }
        
        // Show backup codes in modal if needed
        function showBackupCodes(codes) {
            // Implementation for showing backup codes in a modal
            alert('Backup codes: ' + codes.join(', '));
        }
    </script>
</body>
</html> 