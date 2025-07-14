<?php
/**
 * CamGovCA Security Functions
 * Implements security best practices for the certificate authority
 */

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validateCSRFToken() {
    $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    $session_token = $_SESSION['csrf_token'] ?? '';
    
    if (empty($token) || empty($session_token)) {
        return false;
    }
    
    return hash_equals($session_token, $token);
}

/**
 * Rate limiting check
 */
function checkRateLimit() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $current_time = time();
    $window = 60; // 1 minute window
    $max_requests = 100; // max requests per window
    
    // Create rate limit file if it doesn't exist
    $rate_limit_file = sys_get_temp_dir() . '/camgovca_rate_limit.json';
    
    if (!file_exists($rate_limit_file)) {
        $rate_data = [];
    } else {
        $rate_data = json_decode(file_get_contents($rate_limit_file), true) ?: [];
    }
    
    // Clean old entries
    $rate_data = array_filter($rate_data, function($entry) use ($current_time, $window) {
        return $entry['time'] > ($current_time - $window);
    });
    
    // Count requests for this IP
    $ip_requests = 0;
    foreach ($rate_data as $entry) {
        if ($entry['ip'] === $ip) {
            $ip_requests++;
        }
    }
    
    // Check if limit exceeded
    if ($ip_requests >= $max_requests) {
        return false;
    }
    
    // Add current request
    $rate_data[] = [
        'ip' => $ip,
        'time' => $current_time
    ];
    
    // Save updated data
    file_put_contents($rate_limit_file, json_encode($rate_data));
    
    return true;
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email address
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate certificate serial number format
 */
function validateSerialNumber($serial) {
    return preg_match('/^CERT-\d{8}-[A-F0-9]{8}$/', $serial);
}

/**
 * Validate reference code format
 */
function validateReferenceCode($ref_code) {
    return preg_match('/^REF-\d{8}-[A-F0-9]{6}$/', $ref_code);
}

/**
 * Generate secure random string
 */
function generateSecureString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Hash sensitive data
 */
function hashSensitiveData($data) {
    return hash('sha256', $data . getenv('CAMGOVCA_SALT') ?: 'default_salt');
}

/**
 * Log security events
 */
function logSecurityEvent($event_type, $details, $user_id = null) {
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'event_type' => $event_type,
        'details' => $details,
        'user_id' => $user_id,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    $log_file = __DIR__ . '/../logs/security.log';
    $log_dir = dirname($log_file);
    
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    file_put_contents($log_file, json_encode($log_entry) . "\n", FILE_APPEND | LOCK_EX);
}

/**
 * Check if request is from allowed origin
 */
function checkOrigin() {
    $allowed_origins = [
        'localhost',
        '127.0.0.1',
        'camgovca.cm',
        'www.camgovca.cm'
    ];
    
    $origin = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_HOST'] ?? '';
    
    foreach ($allowed_origins as $allowed) {
        if (strpos($origin, $allowed) !== false) {
            return true;
        }
    }
    
    return false;
}

/**
 * Validate file upload
 */
function validateFileUpload($file, $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'], $max_size = 5242880) {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    if ($file['size'] > $max_size) {
        return false;
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_types)) {
        return false;
    }
    
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowed_mimes = [
        'pdf' => 'application/pdf',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png'
    ];
    
    if (!isset($allowed_mimes[$file_extension]) || $allowed_mimes[$file_extension] !== $mime_type) {
        return false;
    }
    
    return true;
}

/**
 * Encrypt sensitive data
 */
function encryptData($data, $key = null) {
    if ($key === null) {
        $key = getenv('CAMGOVCA_ENCRYPTION_KEY') ?: 'default_encryption_key_32_chars_long';
    }
    
    $method = 'aes-256-cbc';
    $iv_length = openssl_cipher_iv_length($method);
    $iv = openssl_random_pseudo_bytes($iv_length);
    
    $encrypted = openssl_encrypt($data, $method, $key, 0, $iv);
    
    return base64_encode($iv . $encrypted);
}

/**
 * Decrypt sensitive data
 */
function decryptData($encrypted_data, $key = null) {
    if ($key === null) {
        $key = getenv('CAMGOVCA_ENCRYPTION_KEY') ?: 'default_encryption_key_32_chars_long';
    }
    
    $method = 'aes-256-cbc';
    $iv_length = openssl_cipher_iv_length($method);
    
    $data = base64_decode($encrypted_data);
    $iv = substr($data, 0, $iv_length);
    $encrypted = substr($data, $iv_length);
    
    return openssl_decrypt($encrypted, $method, $key, 0, $iv);
}

/**
 * Validate session integrity
 */
function validateSession() {
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    }
    
    // Regenerate session ID every 30 minutes
    if (time() - $_SESSION['created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
    
    // Check for session hijacking
    if (isset($_SESSION['user_agent'])) {
        if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            session_destroy();
            return false;
        }
    } else {
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    }
    
    return true;
}

/**
 * Set secure session parameters
 */
function setSecureSession() {
    // Only set secure cookies if we're on HTTPS
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    } else {
        ini_set('session.cookie_secure', 0);
    }
    
    // Set other security parameters
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    
    // Set SameSite attribute (works on both HTTP and HTTPS)
    ini_set('session.cookie_samesite', 'Lax'); // Changed from 'Strict' to 'Lax' for better compatibility
    
    session_start();
    validateSession();
}

/**
 * Clean up expired sessions
 */
function cleanupExpiredSessions() {
    $session_lifetime = 3600; // 1 hour
    $session_path = session_save_path();
    
    if (is_dir($session_path)) {
        $files = glob($session_path . '/sess_*');
        $now = time();
        
        foreach ($files as $file) {
            if (is_file($file) && ($now - filemtime($file)) > $session_lifetime) {
                unlink($file);
            }
        }
    }
}
?> 