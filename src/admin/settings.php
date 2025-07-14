<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

$pdo = getDBConnection();
if (!$pdo) {
    die('Database connection failed');
}

// Get system settings
$stmt = $pdo->query("SELECT setting_key, setting_value, description FROM system_settings ORDER BY setting_key");
$settings = $stmt->fetchAll();

// Convert to associative array for easier access
$settingsArray = [];
foreach ($settings as $setting) {
    $settingsArray[$setting['setting_key']] = $setting['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA Admin - System Settings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f6f9;
        }
        .header {
            background: #667eea;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .nav-links {
            display: flex;
            gap: 20px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .page-header {
            margin-bottom: 30px;
        }
        .settings-form {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        .form-group .description {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #5a6fd8;
        }
        .section-header {
            background: #f8f9fa;
            padding: 15px;
            margin: 20px -30px 20px -30px;
            border-bottom: 1px solid #eee;
            font-weight: bold;
            color: #333;
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #667eea;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>System Settings</h1>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="page-header">
            <h2>Configure System Settings</h2>
        </div>
        
        <form class="settings-form" method="POST" action="">
            <div class="section-header">General Settings</div>
            
            <div class="form-group">
                <label for="site_name">Site Name</label>
                <input type="text" id="site_name" name="site_name" value="<?php echo htmlspecialchars($settingsArray['site_name'] ?? 'CamGovCA'); ?>">
                <div class="description">The name of your certification authority website</div>
            </div>
            
            <div class="form-group">
                <label for="site_description">Site Description</label>
                <textarea id="site_description" name="site_description"><?php echo htmlspecialchars($settingsArray['site_description'] ?? 'Cameroon Government Certification Authority'); ?></textarea>
                <div class="description">Brief description of your CA services</div>
            </div>
            
            <div class="form-group">
                <label for="contact_email">Contact Email</label>
                <input type="email" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($settingsArray['contact_email'] ?? 'contact@camgovca.cm'); ?>">
                <div class="description">Primary contact email for inquiries</div>
            </div>
            
            <div class="section-header">Certificate Settings</div>
            
            <div class="form-group">
                <label for="default_cert_validity">Default Certificate Validity (days)</label>
                <input type="number" id="default_cert_validity" name="default_cert_validity" value="<?php echo htmlspecialchars($settingsArray['default_cert_validity'] ?? '365'); ?>">
                <div class="description">Default validity period for issued certificates</div>
            </div>
            
            <div class="form-group">
                <label for="max_cert_validity">Maximum Certificate Validity (days)</label>
                <input type="number" id="max_cert_validity" name="max_cert_validity" value="<?php echo htmlspecialchars($settingsArray['max_cert_validity'] ?? '1095'); ?>">
                <div class="description">Maximum allowed validity period for certificates</div>
            </div>
            
            <div class="form-group">
                <label for="ca_organization">CA Organization</label>
                <input type="text" id="ca_organization" name="ca_organization" value="<?php echo htmlspecialchars($settingsArray['ca_organization'] ?? 'Cameroon Government Certification Authority'); ?>">
                <div class="description">Organization name for the Certificate Authority</div>
            </div>
            
            <div class="section-header">Security Settings</div>
            
            <div class="form-group">
                <label for="password_min_length">Minimum Password Length</label>
                <input type="number" id="password_min_length" name="password_min_length" value="<?php echo htmlspecialchars($settingsArray['password_min_length'] ?? '8'); ?>">
                <div class="description">Minimum required password length for user accounts</div>
            </div>
            
            <div class="form-group">
                <label for="session_timeout">Session Timeout (minutes)</label>
                <input type="number" id="session_timeout" name="session_timeout" value="<?php echo htmlspecialchars($settingsArray['session_timeout'] ?? '30'); ?>">
                <div class="description">Inactive session timeout period</div>
            </div>
            
            <div class="form-group">
                <label for="maintenance_mode">Maintenance Mode</label>
                <label class="toggle-switch">
                    <input type="checkbox" id="maintenance_mode" name="maintenance_mode" <?php echo ($settingsArray['maintenance_mode'] ?? '0') == '1' ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                </label>
                <div class="description">Enable maintenance mode to restrict access</div>
            </div>
            
            <div class="section-header">Email Settings</div>
            
            <div class="form-group">
                <label for="smtp_host">SMTP Host</label>
                <input type="text" id="smtp_host" name="smtp_host" value="<?php echo htmlspecialchars($settingsArray['smtp_host'] ?? 'localhost'); ?>">
                <div class="description">SMTP server hostname</div>
            </div>
            
            <div class="form-group">
                <label for="smtp_port">SMTP Port</label>
                <input type="number" id="smtp_port" name="smtp_port" value="<?php echo htmlspecialchars($settingsArray['smtp_port'] ?? '587'); ?>">
                <div class="description">SMTP server port</div>
            </div>
            
            <div class="form-group">
                <label for="smtp_username">SMTP Username</label>
                <input type="text" id="smtp_username" name="smtp_username" value="<?php echo htmlspecialchars($settingsArray['smtp_username'] ?? ''); ?>">
                <div class="description">SMTP authentication username</div>
            </div>
            
            <div class="form-group">
                <label for="smtp_password">SMTP Password</label>
                <input type="password" id="smtp_password" name="smtp_password" value="<?php echo htmlspecialchars($settingsArray['smtp_password'] ?? ''); ?>">
                <div class="description">SMTP authentication password</div>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <button type="submit" class="btn">Save Settings</button>
            </div>
        </form>
    </div>
</body>
</html> 