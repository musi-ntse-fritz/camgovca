<?php
session_start();
require_once '../config/database.php';
require_once '../includes/audit_logger.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

$pdo = getDBConnection();
if (!$pdo) {
    die('Database connection failed');
}

// Get filter parameters
$filters = [];
$filters['action'] = $_GET['action'] ?? '';
$filters['user_id'] = $_GET['user_id'] ?? '';
$filters['table_name'] = $_GET['table_name'] ?? '';
$filters['date_from'] = $_GET['date_from'] ?? '';
$filters['date_to'] = $_GET['date_to'] ?? '';
$filters['search'] = $_GET['search'] ?? '';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 50;

try {
    $logger = getAuditLogger();
    $logs_data = $logger->getAuditLogs($filters, $page, $limit);
    $logs = $logs_data['logs'];
    $total_count = $logs_data['total_count'];
    $total_pages = $logs_data['total_pages'];
    $current_page = $logs_data['current_page'];
} catch (Exception $e) {
    $logs = [];
    $total_count = 0;
    $total_pages = 0;
    $current_page = 1;
    $error_message = "Error loading audit logs: " . $e->getMessage();
}

// Get unique action types for filter dropdown
$action_types = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT action FROM audit_logs ORDER BY action");
    $action_types = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    // Ignore error
}

// Get unique table names for filter dropdown
$table_names = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT table_name FROM audit_logs WHERE table_name IS NOT NULL ORDER BY table_name");
    $table_names = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    // Ignore error
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA Admin - Audit Logs</title>
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
            max-width: 1400px;
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
            margin-left: 10px;
        }
        .btn:hover {
            background: #5a6fd8;
        }
        .logs-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .logs-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .logs-table th,
        .logs-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .logs-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        .action-type {
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .action-login { background: #d4edda; color: #155724; }
        .action-logout { background: #f8d7da; color: #721c24; }
        .action-create { background: #d1ecf1; color: #0c5460; }
        .action-update { background: #fff3cd; color: #856404; }
        .action-delete { background: #f8d7da; color: #721c24; }
        .action-cert { background: #e2e3e5; color: #383d41; }
        .action-admin { background: #cce5ff; color: #004085; }
        .action-file { background: #d1ecf1; color: #0c5460; }
        .timestamp {
            font-family: monospace;
            font-size: 12px;
            color: #666;
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        .pagination a {
            padding: 8px 12px;
            background: white;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #667eea;
            border-radius: 5px;
        }
        .pagination a.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        .pagination a:hover {
            background: #f8f9fa;
        }
        .filters {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .filter-row {
            display: flex;
            gap: 20px;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .filter-group label {
            font-weight: bold;
            font-size: 12px;
            color: #666;
        }
        .filter-group select,
        .filter-group input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            min-width: 150px;
        }
        .filter-actions {
            display: flex;
            gap: 10px;
            align-items: end;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            flex: 1;
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
        .details-cell {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .details-cell:hover {
            white-space: normal;
            overflow: visible;
            position: relative;
            z-index: 10;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 5px;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Audit Logs</h1>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="certificate-requests.php">Certificate Requests</a>
            <a href="certificates.php">Certificates</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="page-header">
            <h2>System Audit Logs</h2>
            <div>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['export' => 'csv'])); ?>" class="btn">Export CSV</a>
                <a href="audit-logs.php" class="btn">Clear Filters</a>
            </div>
        </div>
        
        <?php if (isset($error_message)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($total_count); ?></div>
                <div class="stat-label">Total Log Entries</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($logs); ?></div>
                <div class="stat-label">Showing</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_pages; ?></div>
                <div class="stat-label">Total Pages</div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filters">
            <form method="GET" action="">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Action Type:</label>
                        <select name="action">
                            <option value="">All Actions</option>
                            <?php foreach ($action_types as $action): ?>
                                <option value="<?php echo htmlspecialchars($action); ?>" <?php echo $filters['action'] === $action ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($action); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Table:</label>
                        <select name="table_name">
                            <option value="">All Tables</option>
                            <?php foreach ($table_names as $table): ?>
                                <option value="<?php echo htmlspecialchars($table); ?>" <?php echo $filters['table_name'] === $table ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($table); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Date From:</label>
                        <input type="date" name="date_from" value="<?php echo htmlspecialchars($filters['date_from']); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label>Date To:</label>
                        <input type="date" name="date_to" value="<?php echo htmlspecialchars($filters['date_to']); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label>Search:</label>
                        <input type="text" name="search" placeholder="Search in details..." value="<?php echo htmlspecialchars($filters['search']); ?>">
                    </div>
                    
                    <div class="filter-actions">
                        <button type="submit" class="btn">Filter</button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Logs Table -->
        <div class="logs-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Table</th>
                        <th>Record ID</th>
                        <th>IP Address</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #666;">
                                No audit log entries found
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?php echo $log['log_id']; ?></td>
                                <td class="timestamp"><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($log['username'] ?? 'System'); ?></td>
                                <td>
                                    <?php 
                                    $action_class = 'action-' . strtolower(explode('_', $log['action'])[0]);
                                    ?>
                                    <span class="action-type <?php echo $action_class; ?>">
                                        <?php echo htmlspecialchars($log['action']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($log['table_name'] ?? 'N/A'); ?></td>
                                <td><?php echo $log['record_id'] ?? 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                <td class="details-cell">
                                    <?php 
                                    if ($log['details']) {
                                        echo htmlspecialchars($log['details']);
                                    } elseif ($log['new_values']) {
                                        $new_values = json_decode($log['new_values'], true);
                                        if (is_array($new_values)) {
                                            echo htmlspecialchars(implode(', ', array_map(function($k, $v) {
                                                return "$k: $v";
                                            }, array_keys($new_values), $new_values)));
                                        } else {
                                            echo htmlspecialchars($log['new_values']);
                                        }
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($current_page > 1): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>">Previous</a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" <?php echo $i == $current_page ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
            <?php endfor; ?>
            
            <?php if ($current_page < $total_pages): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>">Next</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html> 