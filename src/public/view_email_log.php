<?php
/**
 * Email Log Viewer
 * Shows recent emails sent by the system
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login_step1.php');
    exit;
}

$log_file = 'logs/email_log.txt';
$emails = [];

if (file_exists($log_file)) {
    $log_content = file_get_contents($log_file);
    $entries = explode('=== EMAIL LOG ENTRY ===', $log_content);
    
    foreach ($entries as $entry) {
        if (trim($entry)) {
            $lines = explode("\n", trim($entry));
            $email_data = [];
            
            foreach ($lines as $line) {
                if (strpos($line, 'Timestamp:') === 0) {
                    $email_data['timestamp'] = trim(substr($line, 10));
                } elseif (strpos($line, 'To:') === 0) {
                    $email_data['to'] = trim(substr($line, 3));
                } elseif (strpos($line, 'Subject:') === 0) {
                    $email_data['subject'] = trim(substr($line, 8));
                } elseif (strpos($line, 'From:') === 0) {
                    $email_data['from'] = trim(substr($line, 5));
                } elseif (strpos($line, 'Content:') === 0) {
                    // Extract OTP code from content
                    $content_start = strpos($entry, 'Content:') + 8;
                    $content = substr($entry, $content_start);
                    
                    // Look for OTP patterns
                    if (preg_match('/\b\d{6}\b/', $content, $matches)) {
                        $email_data['otp'] = $matches[0];
                    }
                    
                    $email_data['content'] = substr($content, 0, 200) . '...';
                }
            }
            
            if (!empty($email_data)) {
                $emails[] = $email_data;
            }
        }
    }
    
    // Reverse to show newest first
    $emails = array_reverse($emails);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Log Viewer - CamGovCA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .email-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            overflow: hidden;
        }
        .email-header {
            background: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }
        .email-body {
            padding: 15px;
        }
        .otp-badge {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 18px;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-sent {
            background: #d4edda;
            color: #155724;
        }
        .status-logged {
            background: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2><i class="fas fa-envelope"></i> Email Log Viewer</h2>
        <p class="mb-0">Recent emails sent by the CamGovCA system</p>
    </div>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle"></i> Important Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Note:</strong> Due to Resend free tier limitations, emails to non-test addresses are logged locally instead of being sent.</p>
                        <p>Users can find their OTP codes in this log viewer.</p>
                        <p>To enable email delivery to all users, verify your domain at <a href="https://resend.com/domains" target="_blank">resend.com/domains</a></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <h4>Recent Emails (<?php echo count($emails); ?> total)</h4>
                
                <?php if (empty($emails)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No emails found in the log.
                    </div>
                <?php else: ?>
                    <?php foreach (array_slice($emails, 0, 20) as $email): ?>
                        <div class="email-card">
                            <div class="email-header">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <strong>To:</strong> <?php echo htmlspecialchars($email['to'] ?? 'Unknown'); ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Subject:</strong> <?php echo htmlspecialchars($email['subject'] ?? 'Unknown'); ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Time:</strong> <?php echo htmlspecialchars($email['timestamp'] ?? 'Unknown'); ?>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <?php if (isset($email['otp'])): ?>
                                            <span class="otp-badge">OTP: <?php echo $email['otp']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="email-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <strong>Content Preview:</strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($email['content'] ?? 'No content'); ?></small>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <?php if (strpos($email['to'] ?? '', 'fritzntse@gmail.com') !== false): ?>
                                            <span class="status-badge status-sent">Sent via Resend</span>
                                        <?php else: ?>
                                            <span class="status-badge status-logged">Logged Locally</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-4">
        <a href="admin/dashboard.php" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 