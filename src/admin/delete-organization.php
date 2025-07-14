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

$org_id = $_GET['id'] ?? 0;
$confirmed = $_GET['confirm'] ?? false;

// Get organization data
$stmt = $pdo->prepare("SELECT org_id, org_name, org_type, contact_email FROM organizations WHERE org_id = ?");
$stmt->execute([$org_id]);
$org = $stmt->fetch();

if (!$org) {
    header('Location: organizations.php');
    exit();
}

if ($confirmed && $org_id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM organizations WHERE org_id = ?");
        $stmt->execute([$org_id]);
        
        header('Location: organizations.php?deleted=1');
        exit();
    } catch (Exception $e) {
        $error = 'Error deleting organization: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA Admin - Delete Organization</title>
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
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
        .confirm-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            text-align: center;
        }
        .warning-icon {
            font-size: 48px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .org-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: left;
        }
        .org-info strong {
            color: #333;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin: 5px;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Delete Organization</h1>
        <div class="nav-links">
            <a href="organizations.php">Back to Organizations</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="confirm-card">
            <div class="warning-icon">⚠️</div>
            
            <?php if (isset($error)): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <h2>Confirm Organization Deletion</h2>
            <p>Are you sure you want to delete this organization? This action cannot be undone.</p>
            
            <div class="org-info">
                <strong>Organization Name:</strong> <?php echo htmlspecialchars($org['org_name']); ?><br>
                <strong>Type:</strong> <?php echo htmlspecialchars($org['org_type']); ?><br>
                <strong>Contact Email:</strong> <?php echo htmlspecialchars($org['contact_email']); ?>
            </div>
            
            <div style="margin-top: 30px;">
                <a href="delete-organization.php?id=<?php echo $org_id; ?>&confirm=1" class="btn btn-danger" onclick="return confirm('Are you absolutely sure? This will permanently delete the organization.')">Delete Organization</a>
                <a href="organizations.php" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </div>
</body>
</html> 