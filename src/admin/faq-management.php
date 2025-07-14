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

// Get all FAQ entries
$stmt = $pdo->query("SELECT faq_id, question, answer, category, language, status, sort_order, created_at FROM faq_entries ORDER BY sort_order, created_at DESC");
$faqs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA Admin - FAQ Management</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .faq-grid {
            display: grid;
            gap: 20px;
        }
        .faq-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .faq-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        .faq-question {
            font-weight: bold;
            color: #333;
            font-size: 16px;
            margin: 0;
            flex: 1;
        }
        .faq-meta {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        .faq-category {
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .faq-language {
            background: #d1ecf1;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .faq-status {
            background: #d4edda;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            color: #155724;
        }
        .faq-answer {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .faq-actions {
            display: flex;
            gap: 10px;
        }
        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
        }
        .btn-edit {
            background: #17a2b8;
        }
        .btn-delete {
            background: #dc3545;
        }
        .btn-toggle {
            background: #ffc107;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FAQ Management</h1>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="page-header">
            <h2>Frequently Asked Questions</h2>
            <a href="#" class="btn">Add New FAQ</a>
        </div>
        
        <div class="faq-grid">
            <?php foreach ($faqs as $faq): ?>
            <div class="faq-card">
                <div class="faq-header">
                    <h3 class="faq-question"><?php echo htmlspecialchars($faq['question']); ?></h3>
                    <div class="faq-actions">
                        <a href="#" class="btn btn-small btn-edit">Edit</a>
                        <a href="#" class="btn btn-small btn-toggle">Toggle</a>
                        <a href="#" class="btn btn-small btn-delete">Delete</a>
                    </div>
                </div>
                
                <div class="faq-meta">
                    <span class="faq-category"><?php echo htmlspecialchars($faq['category']); ?></span>
                    <span class="faq-language"><?php echo strtoupper(htmlspecialchars($faq['language'])); ?></span>
                    <span class="faq-status"><?php echo ucfirst(htmlspecialchars($faq['status'])); ?></span>
                    <span>Order: <?php echo $faq['sort_order']; ?></span>
                </div>
                
                <div class="faq-answer">
                    <?php echo htmlspecialchars(substr($faq['answer'], 0, 200)) . (strlen($faq['answer']) > 200 ? '...' : ''); ?>
                </div>
                
                <div style="color: #999; font-size: 12px;">
                    Created: <?php echo date('M j, Y', strtotime($faq['created_at'])); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html> 