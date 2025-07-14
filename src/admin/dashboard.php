<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

// Include database configuration
require_once '../config/database.php';

$pdo = getDBConnection();
if (!$pdo) {
    die('Database connection failed');
}

// Get statistics with error handling
$stats = [];
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $stats['users'] = $stmt->fetch()['count'];
} catch (Exception $e) {
    $stats['users'] = 0;
}

try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM certificates");
    $stats['certificates'] = $stmt->fetch()['count'];
} catch (Exception $e) {
    $stats['certificates'] = 0;
}

try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM organizations");
    $stats['organizations'] = $stmt->fetch()['count'];
} catch (Exception $e) {
    $stats['organizations'] = 0;
}

try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM certificate_requests WHERE status = 'pending'");
    $stats['pending_requests'] = $stmt->fetch()['count'];
} catch (Exception $e) {
    $stats['pending_requests'] = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA Admin - Dashboard</title>
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
        .logout {
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
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .menu-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .menu-card h3 {
            margin-top: 0;
            color: #333;
        }
        .menu-list {
            list-style: none;
            padding: 0;
        }
        .menu-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .menu-list li:last-child {
            border-bottom: none;
        }
        .menu-list a {
            color: #667eea;
            text-decoration: none;
            display: block;
            padding: 5px 0;
        }
        .menu-list a:hover {
            color: #5a6fd8;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CamGovCA Admin Dashboard</h1>
        <div>
            Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?> | 
            <a href="logout_simple.php" class="logout">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['users']; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['certificates']; ?></div>
                <div class="stat-label">Certificates</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['organizations']; ?></div>
                <div class="stat-label">Organizations</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['pending_requests']; ?></div>
                <div class="stat-label">Pending Requests</div>
            </div>
        </div>
        
        <div class="menu-grid">
            <div class="menu-card">
                <h3>Certificate Management</h3>
                <ul class="menu-list">
                    <li><a href="certificates.php">View All Certificates</a></li>
                    <li><a href="certificate-requests.php">Certificate Requests</a></li>
                    <li><a href="revoke-certificate.php">Revoke Certificates</a></li>
                    <li><a href="certificate-templates.php">Certificate Templates</a></li>
                </ul>
            </div>
            
            <div class="menu-card">
                <h3>User Management</h3>
                <ul class="menu-list">
                    <li><a href="users.php">Manage Users</a></li>
                    <li><a href="organizations.php">Manage Organizations</a></li>
                    <li><a href="ra-management.php">RA Management</a></li>
                    <li><a href="user-roles.php">User Roles & Permissions</a></li>
                </ul>
            </div>
            
            <div class="menu-card">
                <h3>System Administration</h3>
                <ul class="menu-list">
                    <li><a href="settings.php">System Settings</a></li>
                    <li><a href="audit-logs.php">Audit Logs</a></li>
                    <li><a href="backup-restore.php">Backup & Restore</a></li>
                    <li><a href="system-monitoring.php">System Monitoring</a></li>
                </ul>
            </div>
            
            <div class="menu-card">
                <h3>Content Management</h3>
                <ul class="menu-list">
                    <li><a href="faq-management.php">FAQ Management</a></li>
                    <li><a href="news-announcements.php">News & Announcements</a></li>
                    <li><a href="documentation.php">Documentation</a></li>
                    <li><a href="contact-info.php">Contact Information</a></li>
                </ul>
            </div>
        </div>

        <div class="dashboard-section">
            <h3>Security Management</h3>
            <div class="dashboard-links">
                <a href="audit-logs.php" class="dashboard-link">
                    <i class="fas fa-shield-alt"></i>
                    <span>Audit Logs</span>
                </a>
                <a href="2fa-settings.php" class="dashboard-link">
                    <i class="fas fa-key"></i>
                    <span>2FA Settings</span>
                </a>
                <a href="user-management.php" class="dashboard-link">
                    <i class="fas fa-users"></i>
                    <span>User Management</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html> 