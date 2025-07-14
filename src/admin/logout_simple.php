<?php
/**
 * Simple Logout Page
 * Works even if there are PHP configuration issues
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out - CamGovCA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logout-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        .logout-icon {
            font-size: 48px;
            color: #667eea;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .manual-logout {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        .manual-logout h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 16px;
        }
        .manual-logout p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="logout-icon">🔐</div>
        <h1>Successfully Logged Out</h1>
        <p>You have been successfully logged out of the CamGovCA administrative system.</p>
        
        <a href="../login_step1.php" class="btn">Go to Login Page</a>
        
        <div class="manual-logout">
            <h3>If you're still logged in:</h3>
            <p>Clear your browser cookies or try accessing the login page directly.</p>
        </div>
    </div>
    
    <script>
        // Additional cleanup - clear any localStorage or sessionStorage
        if (typeof localStorage !== 'undefined') {
            localStorage.clear();
        }
        if (typeof sessionStorage !== 'undefined') {
            sessionStorage.clear();
        }
        
        // Redirect to login page after 3 seconds
        setTimeout(function() {
            window.location.href = '../login_step1.php';
        }, 3000);
    </script>
</body>
</html> 