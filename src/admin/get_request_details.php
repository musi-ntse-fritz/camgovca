<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

// Include database configuration
require_once '../config/database.php';

$request_id = $_GET['request_id'] ?? '';

if (empty($request_id)) {
    echo json_encode(['success' => false, 'message' => 'ID de demande requis']);
    exit();
}

if (checkDatabaseConnection()) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT 
                cr.*,
                COALESCE(u.first_name, 'N/A') as first_name,
                COALESCE(u.last_name, 'N/A') as last_name,
                COALESCE(u.email, cr.email) as user_email,
                COALESCE(u.phone, cr.phone) as user_phone
            FROM certificate_requests cr
            LEFT JOIN users u ON cr.user_id = u.user_id
            WHERE cr.request_id = ?
        ");
        $stmt->execute([$request_id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($request) {
            // Use user data if available, otherwise use request data
            $request['email'] = $request['user_email'] !== 'N/A' ? $request['user_email'] : $request['email'];
            $request['phone'] = $request['user_phone'] !== 'N/A' ? $request['user_phone'] : $request['phone'];
            
            // Remove duplicate fields
            unset($request['user_email'], $request['user_phone']);
            
            echo json_encode(['success' => true, 'request' => $request]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Demande non trouvée']);
        }
    } catch (Exception $e) {
        error_log("Error fetching request details: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Erreur de base de données']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
}
?> 