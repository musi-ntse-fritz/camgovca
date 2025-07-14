<?php
/**
 * CamGovCA Admin Certificate Operations Handler
 * Secure backend logic for administrative certificate management
 */

// Enable error reporting for development (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start secure session
require_once '../includes/security_functions.php';
setSecureSession();

// Include required files
require_once '../config/database.php';
require_once '../includes/certificate_password_manager.php';
require_once '../includes/2fa_verification.php';
require_once '../includes/audit_logger.php';

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

// Check admin authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login_step1.php');
    exit();
}

// Get operation from URL
$operation = $_GET['operation'] ?? '';
$response = ['success' => false, 'message' => '', 'data' => null];

// Validate operation
$valid_operations = ['bulk-approve', 'bulk-reject', 'bulk-revoke', 'bulk-suspend', 'bulk-resume', 'audit-log', 'system-status', 'backup', 'restore'];
if (!in_array($operation, $valid_operations)) {
    $response['message'] = 'Opération administrative invalide';
    http_response_code(400);
    echo json_encode($response);
    exit();
}

// Rate limiting check for admin operations
if (!checkRateLimit()) {
    $response['message'] = 'Trop de requêtes administratives. Veuillez réessayer plus tard.';
    http_response_code(429);
    echo json_encode($response);
    exit();
}

// CSRF protection for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken()) {
        $response['message'] = 'Token de sécurité invalide';
        http_response_code(403);
        echo json_encode($response);
        exit();
    }
}

try {
    $pdo = getDBConnection();
    $passwordManager = new CertificatePasswordManager($pdo);
    $verification = get2FAVerification($pdo);
    $logger = getAuditLogger();
    
    // Check if 2FA is required for admin operations
    if ($verification->is2FARequired('admin_' . $operation)) {
        // Handle 2FA verification
        if (isset($_POST['2fa_code'])) {
            $code = trim($_POST['2fa_code']);
            $result = $verification->verify2FACode($code, 'admin_' . $operation, $_SESSION['admin_user_id']);
            
            if (!$result['success']) {
                $response['message'] = $result['message'];
                http_response_code(403);
                echo json_encode($response);
                exit();
            }
        } else {
            // Get admin email from session or database
            $admin_email = $_SESSION['admin_email'] ?? null;
            if (!$admin_email) {
                // Fallback: get email from database
                $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
                $stmt->execute([$_SESSION['admin_user_id']]);
                $admin_user = $stmt->fetch();
                $admin_email = $admin_user['email'] ?? null;
            }
            
            // Generate and send 2FA code
            $result = $verification->generate2FACode('admin_' . $operation, $_SESSION['admin_user_id'], $admin_email);
            
            if ($result['success']) {
                $response['require_2fa'] = true;
                $response['message'] = 'Code de vérification envoyé à votre email.';
                echo json_encode($response);
                exit();
            } else {
                $response['message'] = 'Erreur lors de l\'envoi du code de vérification: ' . $result['message'];
                http_response_code(500);
                echo json_encode($response);
                exit();
            }
        }
    }
    
    switch ($operation) {
        case 'bulk-approve':
            $response = handleBulkApprove($pdo, $passwordManager, $logger);
            break;
            
        case 'bulk-reject':
            $response = handleBulkReject($pdo, $logger);
            break;
            
        case 'bulk-revoke':
            $response = handleBulkRevoke($pdo, $passwordManager, $logger);
            break;
            
        case 'bulk-suspend':
            $response = handleBulkSuspend($pdo, $passwordManager, $logger);
            break;
            
        case 'bulk-resume':
            $response = handleBulkResume($pdo, $logger);
            break;
            
        case 'audit-log':
            $response = getAuditLog($pdo);
            break;
            
        case 'system-status':
            $response = getSystemStatus($pdo);
            break;
            
        case 'backup':
            $response = createBackup($pdo, $logger);
            break;
            
        case 'restore':
            $response = restoreBackup($pdo, $logger);
            break;
    }
    
} catch (Exception $e) {
    error_log("Admin certificate operation error: " . $e->getMessage());
    logSecurityEvent('admin_error', $e->getMessage(), $_SESSION['admin_user_id'] ?? null);
    $response['message'] = 'Une erreur s\'est produite lors du traitement de votre demande';
    http_response_code(500);
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

/**
 * Handle bulk certificate approval
 */
function handleBulkApprove($pdo, $passwordManager, $logger) {
    $request_ids = $_POST['request_ids'] ?? [];
    
    if (empty($request_ids) || !is_array($request_ids)) {
        return ['success' => false, 'message' => 'Aucune demande sélectionnée'];
    }
    
    $approved_count = 0;
    $errors = [];
    
    try {
        $pdo->beginTransaction();
        
        foreach ($request_ids as $request_id) {
            try {
                // Get request details
                $stmt = $pdo->prepare("SELECT * FROM certificate_requests WHERE request_id = ? AND status = 'pending'");
                $stmt->execute([$request_id]);
                $request = $stmt->fetch();
                
                if (!$request) {
                    $errors[] = "Demande #$request_id non trouvée ou déjà traitée";
                    continue;
                }
                
                // Update request status
                $stmt = $pdo->prepare("UPDATE certificate_requests SET status = 'approved', processed_at = NOW(), processed_by = ? WHERE request_id = ?");
                $stmt->execute([$_SESSION['admin_user_id'], $request_id]);
                
                // Generate certificate
                $serial_number = 'CERT-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
                $valid_from = date('Y-m-d H:i:s');
                $valid_to = date('Y-m-d H:i:s', strtotime('+1 year'));
                
                // Create certificate record
                $stmt = $pdo->prepare("
                    INSERT INTO certificates 
                    (user_id, org_id, serial_number, subject_dn, subject_alt_names, cert_type, status, valid_from, valid_to, issue_date, notes, ref_code, subject_name, email, country, organization)
                    VALUES (?, ?, ?, ?, ?, ?, 'active', ?, ?, NOW(), ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $request['user_id'],
                    null,
                    $serial_number,
                    $request['subject_dn'],
                    '',
                    $request['cert_type'],
                    $valid_from,
                    $valid_to,
                    'Certificate issued from bulk approved request #' . $request_id,
                    $request['ref_code'],
                    $request['subject_dn'],
                    $request['email'],
                    $request['country'],
                    $request['organization']
                ]);
                
                $approved_count++;
                
                // Log the approval
                logSecurityEvent('certificate_approved', "Bulk approved request #$request_id", $_SESSION['admin_user_id']);
                
            } catch (Exception $e) {
                $errors[] = "Erreur lors de l'approbation de la demande #$request_id: " . $e->getMessage();
            }
        }
        
        $pdo->commit();
        
        return [
            'success' => true,
            'message' => "$approved_count certificat(s) approuvé(s) avec succès",
            'data' => [
                'approved_count' => $approved_count,
                'errors' => $errors
            ]
        ];
        
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
}

/**
 * Handle bulk certificate rejection
 */
function handleBulkReject($pdo, $logger) {
    $request_ids = $_POST['request_ids'] ?? [];
    $rejection_reason = $_POST['rejection_reason'] ?? 'Rejet administratif';
    
    if (empty($request_ids) || !is_array($request_ids)) {
        return ['success' => false, 'message' => 'Aucune demande sélectionnée'];
    }
    
    $rejected_count = 0;
    $errors = [];
    
    try {
        $pdo->beginTransaction();
        
        foreach ($request_ids as $request_id) {
            try {
                $stmt = $pdo->prepare("UPDATE certificate_requests SET status = 'rejected', rejection_reason = ?, processed_at = NOW(), processed_by = ? WHERE request_id = ? AND status = 'pending'");
                $result = $stmt->execute([$rejection_reason, $_SESSION['admin_user_id'], $request_id]);
                
                if ($stmt->rowCount() > 0) {
                    $rejected_count++;
                    logSecurityEvent('certificate_rejected', "Bulk rejected request #$request_id", $_SESSION['admin_user_id']);
                } else {
                    $errors[] = "Demande #$request_id non trouvée ou déjà traitée";
                }
                
            } catch (Exception $e) {
                $errors[] = "Erreur lors du rejet de la demande #$request_id: " . $e->getMessage();
            }
        }
        
        $pdo->commit();
        
        return [
            'success' => true,
            'message' => "$rejected_count demande(s) rejetée(s) avec succès",
            'data' => [
                'rejected_count' => $rejected_count,
                'errors' => $errors
            ]
        ];
        
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
}

/**
 * Get audit log
 */
function getAuditLog($pdo) {
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = 50;
    $offset = ($page - 1) * $limit;
    
    // Get total count
    $stmt = $pdo->query("SELECT COUNT(*) FROM audit_logs");
    $total_count = $stmt->fetchColumn();
    
    // Get audit logs
    $stmt = $pdo->prepare("
        SELECT al.*, u.username, u.email 
        FROM audit_logs al 
        LEFT JOIN users u ON al.user_id = u.user_id 
        ORDER BY al.timestamp DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$limit, $offset]);
    $logs = $stmt->fetchAll();
    
    return [
        'success' => true,
        'message' => 'Journal d\'audit récupéré',
        'data' => [
            'logs' => $logs,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total_count / $limit),
                'total_count' => $total_count
            ]
        ]
    ];
}

/**
 * Get system status
 */
function getSystemStatus($pdo) {
    $status = [];
    
    // Database status
    try {
        $stmt = $pdo->query("SELECT 1");
        $status['database'] = 'online';
    } catch (Exception $e) {
        $status['database'] = 'offline';
    }
    
    // Certificate statistics
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM certificates");
    $status['total_certificates'] = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) as active FROM certificates WHERE status = 'active'");
    $status['active_certificates'] = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) as pending FROM certificate_requests WHERE status = 'pending'");
    $status['pending_requests'] = $stmt->fetchColumn();
    
    // System information
    $status['php_version'] = PHP_VERSION;
    $status['server_time'] = date('Y-m-d H:i:s');
    $status['memory_usage'] = memory_get_usage(true);
    $status['disk_free_space'] = disk_free_space('/');
    
    return [
        'success' => true,
        'message' => 'Statut du système récupéré',
        'data' => $status
    ];
}

/**
 * Create database backup
 */
function createBackup($pdo, $logger) {
    $backup_dir = __DIR__ . '/../backups';
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir, 0755, true);
    }
    
    $timestamp = date('Y-m-d_H-i-s');
    $backup_file = $backup_dir . "/camgovca_backup_$timestamp.sql";
    
    // Get database configuration
    $host = 'localhost';
    $dbname = 'camgovca_db';
    $username = 'root';
    
    // Create backup using mysqldump
    $command = "C:\\xampp\\mysql\\bin\\mysqldump.exe -h $host -u $username --no-tablespaces $dbname > \"$backup_file\"";
    exec($command, $output, $return_var);
    
    if ($return_var !== 0) {
        return ['success' => false, 'message' => 'Erreur lors de la création de la sauvegarde'];
    }
    
    logSecurityEvent('backup_created', "Backup created: $backup_file", $_SESSION['admin_user_id']);
    
    return [
        'success' => true,
        'message' => 'Sauvegarde créée avec succès',
        'data' => [
            'backup_file' => basename($backup_file),
            'file_size' => filesize($backup_file)
        ]
    ];
}

/**
 * Restore database backup
 */
function restoreBackup($pdo, $logger) {
    $backup_file = $_POST['backup_file'] ?? '';
    
    if (empty($backup_file)) {
        return ['success' => false, 'message' => 'Fichier de sauvegarde non spécifié'];
    }
    
    $backup_path = __DIR__ . '/../backups/' . basename($backup_file);
    
    if (!file_exists($backup_path)) {
        return ['success' => false, 'message' => 'Fichier de sauvegarde non trouvé'];
    }
    
    // Get database configuration
    $host = 'localhost';
    $dbname = 'camgovca_db';
    $username = 'root';
    
    // Restore backup using mysql
    $command = "C:\\xampp\\mysql\\bin\\mysql.exe -h $host -u $username $dbname < \"$backup_path\"";
    exec($command, $output, $return_var);
    
    if ($return_var !== 0) {
        return ['success' => false, 'message' => 'Erreur lors de la restauration de la sauvegarde'];
    }
    
    logSecurityEvent('backup_restored', "Backup restored: $backup_file", $_SESSION['admin_user_id']);
    
    return [
        'success' => true,
        'message' => 'Sauvegarde restaurée avec succès'
    ];
}
?> 