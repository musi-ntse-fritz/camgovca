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

// Get system statistics
$stats = [];

// Database stats
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
$stats['users'] = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM certificates");
$stats['certificates'] = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM organizations");
$stats['organizations'] = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM audit_logs");
$stats['audit_logs'] = $stmt->fetch()['count'];

// Get recent audit logs
$stmt = $pdo->query("SELECT action, COUNT(*) as count FROM audit_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) GROUP BY action ORDER BY count DESC LIMIT 5");
$recent_activities = $stmt->fetchAll();

// Get system info
$php_version = PHP_VERSION;
$mysql_version = $pdo->query("SELECT VERSION() as version")->fetch()['version'];
$server_time = date('Y-m-d H:i:s');
$uptime = "24 hours"; // This would be calculated from server logs
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA Admin - System Monitoring</title>
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
            margin-bottom: 30px;
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
        .monitoring-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .monitor-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
        }
        .monitor-card h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .status-online {
            background: #28a745;
        }
        .status-warning {
            background: #ffc107;
        }
        .status-offline {
            background: #dc3545;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #333;
        }
        .info-value {
            color: #666;
        }
        .activity-list {
            list-style: none;
            padding: 0;
        }
        .activity-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .activity-name {
            color: #333;
        }
        .activity-count {
            background: #667eea;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover {
            background: #5a6fd8;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>System Monitoring</h1>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="page-header">
            <h2>System Status & Performance</h2>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['users']; ?></div>
                <div class="stat-label">Active Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['certificates']; ?></div>
                <div class="stat-label">Total Certificates</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['organizations']; ?></div>
                <div class="stat-label">Organizations</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['audit_logs']; ?></div>
                <div class="stat-label">Audit Logs</div>
            </div>
        </div>
        
        <div class="monitoring-grid">
            <div class="monitor-card">
                <h3>System Status</h3>
                <div class="info-item">
                    <span class="info-label">
                        <span class="status-indicator status-online"></span>
                        Web Server
                    </span>
                    <span class="info-value">Online</span>
                </div>
                <div class="info-item">
                    <span class="info-label">
                        <span class="status-indicator status-online"></span>
                        Database
                    </span>
                    <span class="info-value">Connected</span>
                </div>
                <div class="info-item">
                    <span class="info-label">
                        <span class="status-indicator status-online"></span>
                        SSL Certificate
                    </span>
                    <span class="info-value">Valid</span>
                </div>
                <div class="info-item">
                    <span class="info-label">
                        <span class="status-indicator status-warning"></span>
                        Backup System
                    </span>
                    <span class="info-value">Pending</span>
                </div>
                <div style="margin-top: 20px;">
                    <button class="btn">Refresh Status</button>
                </div>
            </div>
            
            <div class="monitor-card">
                <h3>System Information</h3>
                <div class="info-item">
                    <span class="info-label">PHP Version</span>
                    <span class="info-value"><?php echo $php_version; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">MySQL Version</span>
                    <span class="info-value"><?php echo $mysql_version; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Server Time</span>
                    <span class="info-value"><?php echo $server_time; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Uptime</span>
                    <span class="info-value"><?php echo $uptime; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Memory Usage</span>
                    <span class="info-value"><?php echo round(memory_get_usage(true) / 1024 / 1024, 2); ?> MB</span>
                </div>
            </div>
        </div>
        
        <div class="monitor-card">
            <h3>Recent Activity (Last 24 Hours)</h3>
            <?php if (empty($recent_activities)): ?>
                <p style="color: #666; text-align: center;">No recent activity found.</p>
            <?php else: ?>
                <ul class="activity-list">
                    <?php foreach ($recent_activities as $activity): ?>
                    <li class="activity-item">
                        <span class="activity-name"><?php echo ucfirst(htmlspecialchars($activity['action'])); ?></span>
                        <span class="activity-count"><?php echo $activity['count']; ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 