<?php
// Session configuration for CamGovCA
// This file should be included at the beginning of all admin pages

// Configure session for HTTP development environment
ini_set('session.cookie_secure', '0');  // Allow cookies on HTTP
ini_set('session.cookie_httponly', '1'); // Still keep httpOnly for security
ini_set('session.cookie_samesite', 'Lax'); // Allow cross-site requests
ini_set('session.use_strict_mode', '1'); // Use strict mode for security

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is authenticated
function isAdminAuthenticated() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Function to redirect to login if not authenticated
function requireAdminAuth() {
    if (!isAdminAuthenticated()) {
        header('Location: index.php');
        exit();
    }
}

// Function to get current admin user info
function getCurrentAdminUser() {
    if (isAdminAuthenticated()) {
        return [
            'user_id' => $_SESSION['admin_user_id'] ?? null,
            'username' => $_SESSION['admin_username'] ?? null
        ];
    }
    return null;
}
?> 