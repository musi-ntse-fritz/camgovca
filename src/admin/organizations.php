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

// Get all organizations
$stmt = $pdo->query("SELECT org_id, org_name, org_type, contact_email, contact_phone, address, status, created_at FROM organizations ORDER BY created_at DESC");
$organizations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA Admin - Organization Management</title>
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
        .organizations-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .organizations-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .organizations-table th,
        .organizations-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .organizations-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        .status-inactive {
            color: #dc3545;
            font-weight: bold;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .org-type {
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .actions {
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
        .btn-view {
            background: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Organization Management</h1>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="page-header">
            <h2>All Organizations</h2>
            <a href="add-organization.php" class="btn">Add New Organization</a>
        </div>
        
        <div class="organizations-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Organization Name</th>
                        <th>Type</th>
                        <th>Contact Email</th>
                        <th>Contact Phone</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($organizations as $org): ?>
                    <tr>
                        <td><?php echo $org['org_id']; ?></td>
                        <td><?php echo htmlspecialchars($org['org_name']); ?></td>
                        <td><span class="org-type"><?php echo htmlspecialchars($org['org_type']); ?></span></td>
                        <td><?php echo htmlspecialchars($org['contact_email']); ?></td>
                        <td><?php echo htmlspecialchars($org['contact_phone']); ?></td>
                        <td>
                            <span class="status-<?php echo $org['status']; ?>">
                                <?php echo ucfirst(htmlspecialchars($org['status'])); ?>
                            </span>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($org['created_at'])); ?></td>
                        <td>
                            <div class="actions">
                                <a href="#" class="btn btn-small btn-view">View</a>
                                <a href="edit-organization.php?id=<?php echo $org['org_id']; ?>" class="btn btn-small btn-edit">Edit</a>
                                <a href="delete-organization.php?id=<?php echo $org['org_id']; ?>" class="btn btn-small btn-delete">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html> 