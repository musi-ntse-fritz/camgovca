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

// Get contact information from system settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key LIKE '%contact%' OR setting_key LIKE '%email%' OR setting_key LIKE '%phone%' OR setting_key LIKE '%address%'");
$contact_settings = $stmt->fetchAll();

// Convert to associative array
$contact_data = [];
foreach ($contact_settings as $setting) {
    $contact_data[$setting['setting_key']] = $setting['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA Admin - Contact Information</title>
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
        .contact-form {
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
        .contact-preview {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
        }
        .contact-preview h3 {
            margin-top: 0;
            color: #333;
        }
        .contact-item {
            margin-bottom: 10px;
        }
        .contact-label {
            font-weight: bold;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Contact Information</h1>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="page-header">
            <h2>Manage Contact Information</h2>
        </div>
        
        <form class="contact-form" method="POST" action="">
            <div class="section-header">General Contact</div>
            
            <div class="form-group">
                <label for="contact_email">Primary Email</label>
                <input type="email" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($contact_data['contact_email'] ?? 'contact@camgovca.cm'); ?>">
                <div class="description">Main contact email for general inquiries</div>
            </div>
            
            <div class="form-group">
                <label for="contact_phone">Phone Number</label>
                <input type="text" id="contact_phone" name="contact_phone" value="<?php echo htmlspecialchars($contact_data['contact_phone'] ?? '+237 222 123 456'); ?>">
                <div class="description">Main office phone number</div>
            </div>
            
            <div class="form-group">
                <label for="contact_address">Address</label>
                <textarea id="contact_address" name="contact_address"><?php echo htmlspecialchars($contact_data['contact_address'] ?? 'Yaounde, Cameroon'); ?></textarea>
                <div class="description">Physical address of the office</div>
            </div>
            
            <div class="section-header">Technical Support</div>
            
            <div class="form-group">
                <label for="support_email">Technical Support Email</label>
                <input type="email" id="support_email" name="support_email" value="<?php echo htmlspecialchars($contact_data['support_email'] ?? 'support@camgovca.cm'); ?>">
                <div class="description">Email for technical support and certificate issues</div>
            </div>
            
            <div class="form-group">
                <label for="support_phone">Support Phone</label>
                <input type="text" id="support_phone" name="support_phone" value="<?php echo htmlspecialchars($contact_data['support_phone'] ?? '+237 222 123 457'); ?>">
                <div class="description">Phone number for technical support</div>
            </div>
            
            <div class="section-header">Business Hours</div>
            
            <div class="form-group">
                <label for="business_hours">Business Hours</label>
                <input type="text" id="business_hours" name="business_hours" value="<?php echo htmlspecialchars($contact_data['business_hours'] ?? 'Monday - Friday: 8:00 AM - 5:00 PM'); ?>">
                <div class="description">Office operating hours</div>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <button type="submit" class="btn">Save Contact Information</button>
            </div>
        </form>
        
        <div class="contact-preview">
            <h3>Contact Information Preview</h3>
            <div class="contact-item">
                <span class="contact-label">Email:</span> 
                <?php echo htmlspecialchars($contact_data['contact_email'] ?? 'contact@camgovca.cm'); ?>
            </div>
            <div class="contact-item">
                <span class="contact-label">Phone:</span> 
                <?php echo htmlspecialchars($contact_data['contact_phone'] ?? '+237 222 123 456'); ?>
            </div>
            <div class="contact-item">
                <span class="contact-label">Address:</span> 
                <?php echo htmlspecialchars($contact_data['contact_address'] ?? 'Yaounde, Cameroon'); ?>
            </div>
            <div class="contact-item">
                <span class="contact-label">Support Email:</span> 
                <?php echo htmlspecialchars($contact_data['support_email'] ?? 'support@camgovca.cm'); ?>
            </div>
            <div class="contact-item">
                <span class="contact-label">Support Phone:</span> 
                <?php echo htmlspecialchars($contact_data['support_phone'] ?? '+237 222 123 457'); ?>
            </div>
            <div class="contact-item">
                <span class="contact-label">Business Hours:</span> 
                <?php echo htmlspecialchars($contact_data['business_hours'] ?? 'Monday - Friday: 8:00 AM - 5:00 PM'); ?>
            </div>
        </div>
    </div>
</body>
</html> 