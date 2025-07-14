<?php
/**
 * Admin Logout
 * Handles admin user logout and session cleanup
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log logout event before clearing session
if (isset($_SESSION['user_id']) || isset($_SESSION['admin_user_id'])) {
    try {
        require_once '../config/database.php';
        require_once '../includes/audit_logger.php';
        
        $pdo = getDBConnection();
        if ($pdo) {
            $auditLogger = new AuditLogger($pdo);
            
            $user_id = $_SESSION['user_id'] ?? $_SESSION['admin_user_id'] ?? 'unknown';
            $username = $_SESSION['username'] ?? $_SESSION['admin_username'] ?? 'unknown';
            
            $auditLogger->logSecurityEvent('admin_logout', [
                'user_id' => $user_id,
                'username' => $username
            ]);
        }
    } catch (Exception $e) {
        error_log("Error logging logout: " . $e->getMessage());
    }
}

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Clear any remaining session data
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirect to login page with success message
header('Location: ../login_step1.php?logout=success');
exit;
?> 