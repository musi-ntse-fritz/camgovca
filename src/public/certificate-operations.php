<?php
/**
 * CamGovCA Certificate Operations Handler
 * Centralized backend logic for digital certificate management
 * Implements security best practices and proper validation
 */

// Enable error reporting for development (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Include required files
require_once 'config/database.php';
require_once 'includes/certificate_password_manager.php';
require_once 'includes/security_functions.php';
require_once 'includes/audit_logger.php';

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

// Get operation from URL
$operation = $_GET['operation'] ?? '';
$response = ['success' => false, 'message' => '', 'data' => null];

// Validate operation
$valid_operations = ['request', 'validate', 'issue', 'renew', 'revoke', 'suspend', 'check-status'];
if (!in_array($operation, $valid_operations)) {
    $response['message'] = 'Opération invalide';
    http_response_code(400);
    echo json_encode($response);
    exit();
}

// Rate limiting check
if (!checkRateLimit()) {
    $response['message'] = 'Trop de requêtes. Veuillez réessayer plus tard.';
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
    
    switch ($operation) {
        case 'request':
            $response = handleCertificateRequest($pdo, $passwordManager);
            break;
            
        case 'validate':
            $response = validateCertificateRequest($pdo);
            break;
            
        case 'issue':
            $response = issueCertificate($pdo, $passwordManager);
            break;
            
        case 'renew':
            $response = renewCertificate($pdo, $passwordManager);
            break;
            
        case 'revoke':
            $response = revokeCertificate($pdo, $passwordManager);
            break;
            
        case 'suspend':
            $response = suspendCertificate($pdo, $passwordManager);
            break;
            
        case 'check-status':
            $response = checkCertificateStatus($pdo);
            break;
    }
    
} catch (Exception $e) {
    error_log("Certificate operation error: " . $e->getMessage());
    $response['message'] = 'Une erreur s\'est produite lors du traitement de votre demande';
    http_response_code(500);
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

/**
 * Handle certificate request submission
 */
function handleCertificateRequest($pdo, $passwordManager) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return ['success' => false, 'message' => 'Méthode non autorisée'];
    }
    
    // Validate required fields
    $required_fields = ['request_type', 'cert_type', 'subject_dn', 'email'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            return ['success' => false, 'message' => 'Champ requis manquant: ' . $field];
        }
    }
    
    // Sanitize input
    $request_type = filter_var($_POST['request_type'], FILTER_SANITIZE_STRING);
    $cert_type = filter_var($_POST['cert_type'], FILTER_SANITIZE_STRING);
    $subject_dn = filter_var($_POST['subject_dn'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Adresse email invalide'];
    }
    
    // Validate password
    $requested_password = $_POST['requested_password'] ?? '';
    $generate_password = isset($_POST['generate_password']);
    
    if ($generate_password) {
        $requested_password = $passwordManager->generatePassword();
    } else {
        $passwordErrors = $passwordManager->validatePasswordStrength($requested_password);
        if (!empty($passwordErrors)) {
            return ['success' => false, 'message' => 'Mot de passe invalide: ' . implode(', ', $passwordErrors)];
        }
    }
    
    try {
        $pdo->beginTransaction();
        
        // Create or get user
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, phone, first_name, last_name, user_type, created_at) 
                VALUES (?, ?, ?, ?, ?, 'client', NOW())
            ");
            $stmt->execute([$email, $email, $_POST['phone'] ?? '', $subject_dn, $_POST['organization'] ?? '']);
            $userId = $pdo->lastInsertId();
        } else {
            $userId = $user['user_id'];
        }
        
        // Generate unique codes
        $auth_code = 'AUTH-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $ref_code = 'REF-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
        
        // Create certificate request
        $stmt = $pdo->prepare("
            INSERT INTO certificate_requests 
            (user_id, request_type, cert_type, subject_dn, organization, email, phone, address, country, 
             requested_password, password_generated, password_generated_at, auth_code, ref_code, status, submitted_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, 'pending', NOW())
        ");
        
        $stmt->execute([
            $userId, $request_type, $cert_type, $subject_dn, $_POST['organization'] ?? '', 
            $email, $_POST['phone'] ?? '', $_POST['address'] ?? '', $_POST['country'] ?? '',
            $requested_password, $generate_password ? 1 : 0, $auth_code, $ref_code
        ]);
        
        $request_id = $pdo->lastInsertId();
        $pdo->commit();
        
        return [
            'success' => true,
            'message' => 'Demande de certificat soumise avec succès',
            'data' => [
                'request_id' => $request_id,
                'ref_code' => $ref_code,
                'auth_code' => $auth_code,
                'password' => $generate_password ? $requested_password : null
            ]
        ];
        
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
}

/**
 * Validate certificate request
 */
function validateCertificateRequest($pdo) {
    $request_id = $_GET['request_id'] ?? $_POST['request_id'] ?? '';
    $auth_code = $_GET['auth_code'] ?? $_POST['auth_code'] ?? '';
    
    if (empty($request_id) || empty($auth_code)) {
        return ['success' => false, 'message' => 'Paramètres manquants'];
    }
    
    $stmt = $pdo->prepare("
        SELECT cr.*, u.email, u.first_name, u.last_name 
        FROM certificate_requests cr 
        LEFT JOIN users u ON cr.user_id = u.user_id 
        WHERE cr.request_id = ? AND cr.auth_code = ?
    ");
    $stmt->execute([$request_id, $auth_code]);
    $request = $stmt->fetch();
    
    if (!$request) {
        return ['success' => false, 'message' => 'Demande non trouvée ou code d\'autorisation invalide'];
    }
    
    return [
        'success' => true,
        'message' => 'Demande validée',
        'data' => $request
    ];
}

/**
 * Issue certificate (admin only)
 */
function issueCertificate($pdo, $passwordManager) {
    // Check admin privileges
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        return ['success' => false, 'message' => 'Accès non autorisé'];
    }
    
    $request_id = $_POST['request_id'] ?? '';
    if (empty($request_id)) {
        return ['success' => false, 'message' => 'ID de demande requis'];
    }
    
    try {
        $pdo->beginTransaction();
        
        // Get request details
        $stmt = $pdo->prepare("SELECT * FROM certificate_requests WHERE request_id = ? AND status = 'pending'");
        $stmt->execute([$request_id]);
        $request = $stmt->fetch();
        
        if (!$request) {
            return ['success' => false, 'message' => 'Demande non trouvée ou déjà traitée'];
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
            'Certificate issued from approved request #' . $request_id,
            $request['ref_code'],
            $request['subject_dn'],
            $request['email'],
            $request['country'],
            $request['organization']
        ]);
        
        $cert_id = $pdo->lastInsertId();
        $pdo->commit();
        
        return [
            'success' => true,
            'message' => 'Certificat émis avec succès',
            'data' => [
                'cert_id' => $cert_id,
                'serial_number' => $serial_number,
                'valid_from' => $valid_from,
                'valid_to' => $valid_to
            ]
        ];
        
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
}

/**
 * Renew certificate
 */
function renewCertificate($pdo, $passwordManager) {
    $cert_id = $_POST['cert_id'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($cert_id) || empty($password)) {
        return ['success' => false, 'message' => 'Paramètres manquants'];
    }
    
    // Validate password
    $stmt = $pdo->prepare("SELECT * FROM certificates WHERE cert_id = ?");
    $stmt->execute([$cert_id]);
    $certificate = $stmt->fetch();
    
    if (!$certificate) {
        return ['success' => false, 'message' => 'Certificat non trouvé'];
    }
    
    if (!$passwordManager->validateCertificatePassword($cert_id, $password)) {
        return ['success' => false, 'message' => 'Mot de passe invalide'];
    }
    
    try {
        $pdo->beginTransaction();
        
        // Create new certificate with extended validity
        $new_serial_number = 'CERT-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $valid_from = date('Y-m-d H:i:s');
        $valid_to = date('Y-m-d H:i:s', strtotime('+1 year'));
        
        $stmt = $pdo->prepare("
            INSERT INTO certificates 
            (user_id, org_id, serial_number, subject_dn, subject_alt_names, cert_type, status, valid_from, valid_to, issue_date, notes, ref_code, subject_name, email, country, organization)
            VALUES (?, ?, ?, ?, ?, ?, 'active', ?, ?, NOW(), ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $certificate['user_id'],
            $certificate['org_id'],
            $new_serial_number,
            $certificate['subject_dn'],
            $certificate['subject_alt_names'],
            $certificate['cert_type'],
            $valid_from,
            $valid_to,
            'Certificate renewed from #' . $certificate['cert_id'],
            $certificate['ref_code'],
            $certificate['subject_name'],
            $certificate['email'],
            $certificate['country'],
            $certificate['organization']
        ]);
        
        // Revoke old certificate
        $stmt = $pdo->prepare("UPDATE certificates SET status = 'revoked', revoked_at = NOW(), revocation_reason = 'Renewed' WHERE cert_id = ?");
        $stmt->execute([$cert_id]);
        
        $new_cert_id = $pdo->lastInsertId();
        $pdo->commit();
        
        return [
            'success' => true,
            'message' => 'Certificat renouvelé avec succès',
            'data' => [
                'old_cert_id' => $cert_id,
                'new_cert_id' => $new_cert_id,
                'new_serial_number' => $new_serial_number
            ]
        ];
        
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
}

/**
 * Revoke certificate
 */
function revokeCertificate($pdo, $passwordManager) {
    $cert_id = $_POST['cert_id'] ?? '';
    $password = $_POST['password'] ?? '';
    $reason = $_POST['reason'] ?? 'Administrative revocation';
    
    if (empty($cert_id) || empty($password)) {
        return ['success' => false, 'message' => 'Paramètres manquants'];
    }
    
    // Validate password
    if (!$passwordManager->validateCertificatePassword($cert_id, $password)) {
        return ['success' => false, 'message' => 'Mot de passe invalide'];
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE certificates SET status = 'revoked', revoked_at = NOW(), revocation_reason = ? WHERE cert_id = ? AND status = 'active'");
        $result = $stmt->execute([$reason, $cert_id]);
        
        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'Certificat non trouvé ou déjà révoqué'];
        }
        
        return [
            'success' => true,
            'message' => 'Certificat révoqué avec succès'
        ];
        
    } catch (Exception $e) {
        throw $e;
    }
}

/**
 * Suspend certificate
 */
function suspendCertificate($pdo, $passwordManager) {
    $cert_id = $_POST['cert_id'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($cert_id) || empty($password)) {
        return ['success' => false, 'message' => 'Paramètres manquants'];
    }
    
    // Validate password
    if (!$passwordManager->validateCertificatePassword($cert_id, $password)) {
        return ['success' => false, 'message' => 'Mot de passe invalide'];
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE certificates SET status = 'suspended', updated_at = NOW() WHERE cert_id = ? AND status = 'active'");
        $result = $stmt->execute([$cert_id]);
        
        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'Certificat non trouvé ou déjà suspendu'];
        }
        
        return [
            'success' => true,
            'message' => 'Certificat suspendu avec succès'
        ];
        
    } catch (Exception $e) {
        throw $e;
    }
}

/**
 * Check certificate status
 */
function checkCertificateStatus($pdo) {
    $serial_number = $_GET['serial_number'] ?? $_POST['serial_number'] ?? '';
    $ref_code = $_GET['ref_code'] ?? $_POST['ref_code'] ?? '';
    
    if (empty($serial_number) && empty($ref_code)) {
        return ['success' => false, 'message' => 'Numéro de série ou code de référence requis'];
    }
    
    $where_clause = '';
    $params = [];
    
    if (!empty($serial_number)) {
        $where_clause = "serial_number = ?";
        $params[] = $serial_number;
    } else {
        $where_clause = "ref_code = ?";
        $params[] = $ref_code;
    }
    
    $stmt = $pdo->prepare("
        SELECT cert_id, serial_number, subject_dn, cert_type, status, valid_from, valid_to, issue_date, revoked_at, revocation_reason
        FROM certificates 
        WHERE $where_clause
    ");
    $stmt->execute($params);
    $certificate = $stmt->fetch();
    
    if (!$certificate) {
        return ['success' => false, 'message' => 'Certificat non trouvé'];
    }
    
    return [
        'success' => true,
        'message' => 'Statut du certificat récupéré',
        'data' => $certificate
    ];
}
?> 