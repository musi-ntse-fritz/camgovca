<?php
/**
 * File Upload Handler with Audit Logging
 * Demonstrates how to integrate audit logging with file operations
 */

// Start session
session_start();

// Include required files
require_once 'includes/audit_logger.php';
require_once 'includes/security_functions.php';
require_once 'config/database.php';

// Set secure session
setSecureSession();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['certificate_file'])) {
    $file = $_FILES['certificate_file'];
    
    // Validate file upload
    if (!validateFileUpload($file, ['pdf', 'pem', 'p12', 'crt', 'cer'], 10485760)) { // 10MB max
        $error = 'Invalid file upload. Please check file type and size.';
        
        // Log the failed upload attempt
        $logger = getAuditLogger();
        $logger->logSecurityEvent('file_upload_failed', [
            'file_name' => $file['name'] ?? 'unknown',
            'file_size' => $file['size'] ?? 0,
            'error' => 'File validation failed',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    } else {
        // Create upload directory if it doesn't exist
        $upload_dir = 'uploads/certificates/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Generate unique filename
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $unique_filename = 'cert_' . date('Ymd_His') . '_' . uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $unique_filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $message = 'File uploaded successfully!';
            
            // Log the successful file upload
            $logger = getAuditLogger();
            $logger->logFileOperation('upload', $file_path, [
                'original_name' => $file['name'],
                'file_size' => $file['size'],
                'file_type' => $file['type'],
                'uploaded_by' => $_SESSION['user_id'] ?? 'anonymous'
            ]);
            
            // Also log as a certificate operation if it's a certificate file
            if (in_array($file_extension, ['pem', 'p12', 'crt', 'cer'])) {
                $logger->logCertificateOperation('file_upload', null, null, [
                    'file_path' => $file_path,
                    'file_type' => $file_extension,
                    'file_size' => $file['size']
                ], "Certificate file uploaded");
            }
            
        } else {
            $error = 'Failed to save uploaded file.';
            
            // Log the failed save attempt
            $logger = getAuditLogger();
            $logger->logSecurityEvent('file_save_failed', [
                'file_name' => $file['name'],
                'target_path' => $file_path,
                'error' => 'move_uploaded_file failed'
            ]);
        }
    }
}

// Get recent file operations from audit logs
$recent_files = [];
try {
    $logger = getAuditLogger();
    $logs_data = $logger->getAuditLogs(['action' => 'file_%'], 1, 10);
    $recent_files = $logs_data['logs'];
} catch (Exception $e) {
    // Ignore errors for demo
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload with Audit Logging - CamGovCA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 2px dashed #ddd;
            border-radius: 5px;
        }
        .btn {
            background: #1e3c72;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #2a5298;
        }
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .logs-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        .log-entry {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border-left: 4px solid #1e3c72;
        }
        .log-time {
            color: #666;
            font-size: 12px;
        }
        .log-action {
            font-weight: bold;
            color: #1e3c72;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>File Upload with Audit Logging</h1>
        <p>This demonstrates how file operations are automatically logged to the audit system.</p>
        
        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="certificate_file">Upload Certificate File:</label>
                <input type="file" id="certificate_file" name="certificate_file" accept=".pdf,.pem,.p12,.crt,.cer" required>
                <small>Accepted formats: PDF, PEM, P12, CRT, CER (max 10MB)</small>
            </div>
            
            <button type="submit" class="btn">Upload File</button>
        </form>
        
        <div class="logs-section">
            <h2>Recent File Operations (from Audit Logs)</h2>
            <?php if (empty($recent_files)): ?>
                <p>No recent file operations found.</p>
            <?php else: ?>
                <?php foreach ($recent_files as $log): ?>
                    <div class="log-entry">
                        <div class="log-time"><?php echo htmlspecialchars($log['created_at']); ?></div>
                        <div class="log-action"><?php echo htmlspecialchars($log['action']); ?></div>
                        <div><?php echo htmlspecialchars($log['details'] ?? 'No details'); ?></div>
                        <div><small>IP: <?php echo htmlspecialchars($log['ip_address']); ?></small></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 30px; padding: 20px; background: #e9ecef; border-radius: 5px;">
            <h3>How This Works:</h3>
            <ol>
                <li>When a file is uploaded, the system validates it</li>
                <li>If validation passes, the file is saved to the server</li>
                <li>The audit logger automatically records the file operation</li>
                <li>For certificate files, additional certificate-specific logging occurs</li>
                <li>All operations are stored in the audit_logs table</li>
                <li>Failed uploads are also logged for security monitoring</li>
            </ol>
        </div>
    </div>
</body>
</html> 