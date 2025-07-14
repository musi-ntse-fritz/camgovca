<?php
/**
 * CamGovCA Centralized Audit Logger
 * Automatically records all file operations and database changes
 */

require_once __DIR__ . '/../config/database.php';

class AuditLogger {
    private $pdo;
    private $user_id;
    private $ip_address;
    private $user_agent;
    
    public function __construct($pdo = null) {
        $this->pdo = $pdo ?: getDBConnection();
        $this->user_id = $_SESSION['user_id'] ?? $_SESSION['admin_user_id'] ?? null;
        $this->ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $this->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    }
    
    /**
     * Log a general action
     */
    public function logAction($action, $table_name = null, $record_id = null, $old_values = null, $new_values = null, $details = null) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO audit_logs 
                (user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $this->user_id,
                $action,
                $table_name,
                $record_id,
                $old_values ? json_encode($old_values) : null,
                $new_values ? json_encode($new_values) : null,
                $this->ip_address,
                $this->user_agent
            ]);
            
            return true;
        } catch (Exception $e) {
            error_log("Audit logging error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log file operations
     */
    public function logFileOperation($operation, $file_path, $details = null) {
        return $this->logAction(
            'file_' . $operation,
            'files',
            null,
            null,
            ['file_path' => $file_path],
            $details
        );
    }
    
    /**
     * Log database operations
     */
    public function logDatabaseOperation($operation, $table_name, $record_id, $old_values = null, $new_values = null) {
        return $this->logAction(
            'db_' . $operation,
            $table_name,
            $record_id,
            $old_values,
            $new_values
        );
    }
    
    /**
     * Log user authentication events
     */
    public function logAuthEvent($event_type, $username = null, $success = true, $details = null) {
        return $this->logAction(
            'auth_' . $event_type,
            'users',
            null,
            null,
            [
                'username' => $username,
                'success' => $success,
                'ip_address' => $this->ip_address
            ],
            $details
        );
    }
    
    /**
     * Log certificate operations
     */
    public function logCertificateOperation($operation, $cert_id, $old_values = null, $new_values = null, $details = null) {
        return $this->logAction(
            'cert_' . $operation,
            'certificates',
            $cert_id,
            $old_values,
            $new_values,
            $details
        );
    }
    
    /**
     * Log admin operations
     */
    public function logAdminOperation($operation, $target_table = null, $target_id = null, $details = null) {
        return $this->logAction(
            'admin_' . $operation,
            $target_table,
            $target_id,
            null,
            null,
            $details
        );
    }
    
    /**
     * Log security events
     */
    public function logSecurityEvent($event_type, $details = null) {
        return $this->logAction(
            'security_' . $event_type,
            null,
            null,
            null,
            null,
            $details
        );
    }
    
    /**
     * Log API calls
     */
    public function logAPICall($endpoint, $method, $success = true, $response_code = null, $details = null) {
        return $this->logAction(
            'api_call',
            'api',
            null,
            null,
            [
                'endpoint' => $endpoint,
                'method' => $method,
                'success' => $success,
                'response_code' => $response_code
            ],
            $details
        );
    }
    
    /**
     * Get audit logs with filtering and pagination
     */
    public function getAuditLogs($filters = [], $page = 1, $limit = 50) {
        try {
            $where_conditions = [];
            $params = [];
            
            if (!empty($filters['action'])) {
                $where_conditions[] = "al.action LIKE ?";
                $params[] = '%' . $filters['action'] . '%';
            }
            
            if (!empty($filters['user_id'])) {
                $where_conditions[] = "al.user_id = ?";
                $params[] = $filters['user_id'];
            }
            
            if (!empty($filters['table_name'])) {
                $where_conditions[] = "al.table_name = ?";
                $params[] = $filters['table_name'];
            }
            
            if (!empty($filters['date_from'])) {
                $where_conditions[] = "al.created_at >= ?";
                $params[] = $filters['date_from'] . ' 00:00:00';
            }
            
            if (!empty($filters['date_to'])) {
                $where_conditions[] = "al.created_at <= ?";
                $params[] = $filters['date_to'] . ' 23:59:59';
            }
            
            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
            
            $offset = ($page - 1) * $limit;
            
            // Get total count
            $count_sql = "SELECT COUNT(*) FROM audit_logs al $where_clause";
            $stmt = $this->pdo->prepare($count_sql);
            $stmt->execute($params);
            $total_count = $stmt->fetchColumn();
            
            // Get logs - using direct values for LIMIT/OFFSET to avoid MariaDB parameter issue
            $sql = "
                SELECT al.*, u.username, u.email 
                FROM audit_logs al 
                LEFT JOIN users u ON al.user_id = u.user_id 
                $where_clause
                ORDER BY al.created_at DESC 
                LIMIT " . (int)$limit . " OFFSET " . (int)$offset . "
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'logs' => $logs,
                'total_count' => $total_count,
                'total_pages' => ceil($total_count / $limit),
                'current_page' => $page
            ];
        } catch (Exception $e) {
            error_log("Error getting audit logs: " . $e->getMessage());
            return [
                'logs' => [],
                'total_count' => 0,
                'total_pages' => 0,
                'current_page' => $page
            ];
        }
    }
    
    /**
     * Clean old audit logs
     */
    public function cleanOldLogs($days_to_keep = 90) {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM audit_logs 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
            ");
            $stmt->execute([$days_to_keep]);
            
            $deleted_count = $stmt->rowCount();
            $this->logAdminOperation('cleanup_logs', 'audit_logs', null, "Deleted $deleted_count old log entries");
            
            return $deleted_count;
        } catch (Exception $e) {
            error_log("Error cleaning old audit logs: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Export audit logs to CSV
     */
    public function exportToCSV($filters = [], $filename = null) {
        if (!$filename) {
            $filename = 'audit_logs_' . date('Y-m-d_H-i-s') . '.csv';
        }
        
        $logs_data = $this->getAuditLogs($filters, 1, 10000); // Get all logs
        $logs = $logs_data['logs'];
        
        $csv_data = [];
        $csv_data[] = ['ID', 'Timestamp', 'User', 'Action', 'Table', 'Record ID', 'IP Address', 'Details'];
        
        foreach ($logs as $log) {
            $csv_data[] = [
                $log['log_id'],
                $log['created_at'],
                $log['username'] ?? 'System',
                $log['action'],
                $log['table_name'] ?? 'N/A',
                $log['record_id'] ?? 'N/A',
                $log['ip_address'],
                $log['details'] ?? 'N/A'
            ];
        }
        
        $csv_content = '';
        foreach ($csv_data as $row) {
            $csv_content .= '"' . implode('","', array_map('addslashes', $row)) . '"' . "\n";
        }
        
        return [
            'filename' => $filename,
            'content' => $csv_content,
            'size' => strlen($csv_content)
        ];
    }
}

/**
 * Global audit logger instance
 */
$auditLogger = null;

/**
 * Get or create audit logger instance
 */
function getAuditLogger() {
    global $auditLogger;
    if (!$auditLogger) {
        $auditLogger = new AuditLogger();
    }
    return $auditLogger;
}

/**
 * Convenience functions for common audit operations
 */

function logFileAccess($file_path, $operation = 'access') {
    $logger = getAuditLogger();
    return $logger->logFileOperation($operation, $file_path);
}

function logFileUpload($file_path, $file_size, $file_type) {
    $logger = getAuditLogger();
    return $logger->logFileOperation('upload', $file_path, [
        'file_size' => $file_size,
        'file_type' => $file_type
    ]);
}

function logFileDownload($file_path) {
    $logger = getAuditLogger();
    return $logger->logFileOperation('download', $file_path);
}

function logFileDelete($file_path) {
    $logger = getAuditLogger();
    return $logger->logFileOperation('delete', $file_path);
}

function logUserLogin($username, $success = true, $details = null) {
    $logger = getAuditLogger();
    return $logger->logAuthEvent('login', $username, $success, $details);
}

function logUserLogout($username = null) {
    $logger = getAuditLogger();
    return $logger->logAuthEvent('logout', $username);
}

function logCertificateRequest($request_id, $cert_type, $subject_dn) {
    $logger = getAuditLogger();
    return $logger->logCertificateOperation('request', $request_id, null, [
        'cert_type' => $cert_type,
        'subject_dn' => $subject_dn
    ]);
}

function logCertificateIssuance($cert_id, $serial_number) {
    $logger = getAuditLogger();
    return $logger->logCertificateOperation('issue', $cert_id, null, [
        'serial_number' => $serial_number
    ]);
}

function logCertificateRevocation($cert_id, $reason) {
    $logger = getAuditLogger();
    return $logger->logCertificateOperation('revoke', $cert_id, null, [
        'revocation_reason' => $reason
    ]);
}

function logAdminAction($action, $target = null, $details = null) {
    $logger = getAuditLogger();
    return $logger->logAdminOperation($action, $target, null, $details);
}

function logSecurityViolation($violation_type, $details = null) {
    $logger = getAuditLogger();
    return $logger->logSecurityEvent('violation', [
        'type' => $violation_type,
        'details' => $details
    ]);
}

/**
 * Auto-logging for database operations using PDO wrapper
 */
class AuditedPDO extends PDO {
    private $auditLogger;
    
    public function __construct($dsn, $username = null, $password = null, $options = []) {
        parent::__construct($dsn, $username, $password, $options);
        $this->auditLogger = new AuditLogger($this);
    }
    
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->prepare($sql);
        $stmt->execute($data);
        
        $record_id = $this->lastInsertId();
        $this->auditLogger->logDatabaseOperation('insert', $table, $record_id, null, $data);
        
        return $record_id;
    }
    
    public function update($table, $data, $where, $where_params = []) {
        // Get old values first
        $old_values = null;
        if (!empty($where)) {
            $select_sql = "SELECT * FROM $table WHERE $where";
            $stmt = $this->prepare($select_sql);
            $stmt->execute($where_params);
            $old_values = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        $set_clause = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE $table SET $set_clause WHERE $where";
        
        $stmt = $this->prepare($sql);
        $params = array_merge(array_values($data), $where_params);
        $stmt->execute($params);
        
        $this->auditLogger->logDatabaseOperation('update', $table, $old_values['id'] ?? null, $old_values, $data);
        
        return $stmt->rowCount();
    }
    
    public function delete($table, $where, $where_params = []) {
        // Get old values first
        $old_values = null;
        if (!empty($where)) {
            $select_sql = "SELECT * FROM $table WHERE $where";
            $stmt = $this->prepare($select_sql);
            $stmt->execute($where_params);
            $old_values = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        $sql = "DELETE FROM $table WHERE $where";
        $stmt = $this->prepare($sql);
        $stmt->execute($where_params);
        
        $this->auditLogger->logDatabaseOperation('delete', $table, $old_values['id'] ?? null, $old_values, null);
        
        return $stmt->rowCount();
    }
}
?> 