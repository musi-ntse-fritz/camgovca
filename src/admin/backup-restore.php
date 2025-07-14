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

// Get database size and table information
$stmt = $pdo->query("SELECT table_name, table_rows, ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb' FROM information_schema.tables WHERE table_schema = 'camgovca_db' ORDER BY (data_length + index_length) DESC");
$tables = $stmt->fetchAll();

// Calculate total database size
$total_size = 0;
foreach ($tables as $table) {
    $total_size += $table['size_mb'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA Admin - Backup & Restore</title>
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
        .backup-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .backup-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
        }
        .backup-card h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            margin: 5px;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            color: #666;
            font-size: 12px;
        }
        .tables-list {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
        }
        .tables-list h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .table-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .table-item:last-child {
            border-bottom: none;
        }
        .table-name {
            font-weight: bold;
            color: #333;
        }
        .table-info {
            color: #666;
            font-size: 12px;
        }
        .file-upload {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            margin: 20px 0;
        }
        .file-upload:hover {
            border-color: #667eea;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Backup & Restore</h1>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="page-header">
            <h2>Database Backup & Restore</h2>
        </div>
        
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number"><?php echo count($tables); ?></div>
                <div class="stat-label">Total Tables</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo number_format($total_size, 2); ?> MB</div>
                <div class="stat-label">Database Size</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo date('M j, Y'); ?></div>
                <div class="stat-label">Last Backup</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">0</div>
                <div class="stat-label">Available Backups</div>
            </div>
        </div>
        
        <div class="backup-grid">
            <div class="backup-card">
                <h3>Create Backup</h3>
                <p>Create a complete backup of your database including all tables and data.</p>
                <button class="btn btn-success">Create Full Backup</button>
                <button class="btn btn-warning">Create Structure Only</button>
                <p style="font-size: 12px; color: #666; margin-top: 10px;">
                    Full backup includes all data. Structure backup includes only table schemas.
                </p>
            </div>
            
            <div class="backup-card">
                <h3>Restore Database</h3>
                <p>Restore your database from a previous backup file.</p>
                <div class="file-upload">
                    <p>Drag and drop backup file here or click to browse</p>
                    <input type="file" accept=".sql,.gz" style="display: none;" id="backup-file">
                    <button class="btn" onclick="document.getElementById('backup-file').click()">Choose File</button>
                </div>
                <button class="btn btn-danger">Restore Database</button>
                <p style="font-size: 12px; color: #666; margin-top: 10px;">
                    Warning: This will overwrite current data. Make sure to backup first.
                </p>
            </div>
        </div>
        
        <div class="tables-list">
            <h3>Database Tables</h3>
            <?php foreach ($tables as $table): ?>
            <div class="table-item">
                <div>
                    <div class="table-name"><?php echo htmlspecialchars($table['table_name']); ?></div>
                    <div class="table-info">
                        <?php echo number_format($table['table_rows']); ?> rows | 
                        <?php echo $table['size_mb']; ?> MB
                    </div>
                </div>
                <button class="btn btn-warning" style="font-size: 12px; padding: 5px 10px;">
                    Backup Table
                </button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html> 