<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$pdo = getDBConnection();
if (!$pdo) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$cert_id = $_GET['cert_id'] ?? '';

if (empty($cert_id)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Certificate ID is required']);
    exit();
}

try {
    // Get certificate details
    $stmt = $pdo->prepare("
        SELECT 
            c.cert_id,
            c.serial_number,
            c.subject_dn,
            c.issuer_dn,
            c.cert_type,
            c.status,
            c.valid_from,
            c.valid_to,
            c.created_at,
            c.updated_at,
            u.username,
            u.first_name,
            u.last_name,
            u.email,
            ca.ca_name
        FROM certificates c
        LEFT JOIN users u ON c.user_id = u.user_id
        LEFT JOIN certificate_authorities ca ON c.ca_id = ca.ca_id
        WHERE c.cert_id = ?
    ");
    
    $stmt->execute([$cert_id]);
    $certificate = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($certificate) {
        // Format dates
        $certificate['valid_from'] = date('M j, Y H:i:s', strtotime($certificate['valid_from']));
        $certificate['valid_to'] = date('M j, Y H:i:s', strtotime($certificate['valid_to']));
        $certificate['created_at'] = date('M j, Y H:i:s', strtotime($certificate['created_at']));
        $certificate['updated_at'] = date('M j, Y H:i:s', strtotime($certificate['updated_at']));
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'certificate' => $certificate]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Certificate not found']);
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error fetching certificate details: ' . $e->getMessage()]);
}
?> 