<?php
/**
 * CamGovCA Security Code Implementations
 * 
 * This file contains all the security-related code implementations used in the CamGovCA project.
 * Copy and adapt these functions as needed for your implementation.
 */

// ============================================================================
// TWO-FACTOR AUTHENTICATION (2FA) IMPLEMENTATION
// ============================================================================

/**
 * Generate and store 2FA OTP for user
 */
function generateOTP($user_id) {
    global $pdo;
    
    // Generate 6-digit OTP
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Set expiration time (10 minutes from now)
    $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    
    // Store OTP in database
    $stmt = $pdo->prepare("INSERT INTO 2fa_codes (user_id, code, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $otp, $expires_at]);
    
    return $otp;
}

/**
 * Verify 2FA OTP
 */
function verifyOTP($user_id, $otp) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM 2fa_codes 
                          WHERE user_id = ? AND code = ? AND used = FALSE 
                          AND expires_at > NOW() 
                          ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$user_id, $otp]);
    $otp_record = $stmt->fetch();
    
    if ($otp_record) {
        // Mark OTP as used
        $stmt = $pdo->prepare("UPDATE 2fa_codes SET used = TRUE WHERE id = ?");
        $stmt->execute([$otp_record['id']]);
        return true;
    }
    
    return false;
}

/**
 * Send 2FA OTP via email
 */
function sendOTPEmail($email, $otp, $user_name) {
    $subject = "CamGovCA - Code de Vérification 2FA";
    $message = "
    <html>
    <body>
        <h2>Code de Vérification CamGovCA</h2>
        <p>Bonjour $user_name,</p>
        <p>Votre code de vérification à deux facteurs est :</p>
        <h1 style='font-size: 32px; color: #1e3c72; text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;'>$otp</h1>
        <p><strong>Ce code expire dans 10 minutes.</strong></p>
        <p>Si vous n'avez pas demandé ce code, veuillez ignorer cet email.</p>
        <p>Cordialement,<br>Équipe CamGovCA</p>
    </body>
    </html>";
    
    return sendEmail($email, $subject, $message);
}

// ============================================================================
// PASSWORD SECURITY
// ============================================================================

/**
 * Hash password securely
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Validate password strength
 */
function validatePasswordStrength($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins une lettre majuscule";
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins une lettre minuscule";
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins un chiffre";
    }
    
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins un caractère spécial";
    }
    
    return $errors;
}

// ============================================================================
// INPUT VALIDATION AND SANITIZATION
// ============================================================================

/**
 * Sanitize user input
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email address
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate certificate serial number
 */
function validateCertificateSerial($serial) {
    // Certificate serial format: CERT-YYYYMMDD-XXXXXXXX
    return preg_match('/^CERT-\d{8}-[A-Z0-9]{8}$/', $serial);
}

/**
 * Validate file upload
 */
function validateFileUpload($file, $allowed_types, $max_size = 5242880) {
    $errors = [];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Erreur lors du téléchargement du fichier";
        return $errors;
    }
    
    if ($file['size'] > $max_size) {
        $errors[] = "Le fichier est trop volumineux (max " . ($max_size / 1024 / 1024) . "MB)";
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_types)) {
        $errors[] = "Type de fichier non autorisé";
    }
    
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowed_mimes = [
        'pem' => ['application/x-pem-file', 'text/plain'],
        'crt' => ['application/x-x509-ca-cert', 'text/plain'],
        'cer' => ['application/x-x509-ca-cert', 'text/plain'],
        'p12' => ['application/x-pkcs12'],
        'pfx' => ['application/x-pkcs12']
    ];
    
    if (isset($allowed_mimes[$file_extension])) {
        if (!in_array($mime_type, $allowed_mimes[$file_extension])) {
            $errors[] = "Type MIME non autorisé";
        }
    }
    
    return $errors;
}

// ============================================================================
// SESSION SECURITY
// ============================================================================

/**
 * Configure secure session
 */
function configureSecureSession() {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', 3600); // 1 hour
    
    session_start();
    
    // Regenerate session ID if not authenticated
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        session_regenerate_id(true);
    }
}

/**
 * Validate session
 */
function validateSession() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: index.php');
        exit();
    }
    
    // Check session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {
        session_unset();
        session_destroy();
        header('Location: index.php?timeout=1');
        exit();
    }
    
    $_SESSION['last_activity'] = time();
}

/**
 * Secure logout
 */
function secureLogout() {
    session_unset();
    session_destroy();
    session_start();
    session_regenerate_id(true);
}

// ============================================================================
// DATABASE SECURITY
// ============================================================================

/**
 * Secure database connection
 */
function getSecureDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Execute secure query
 */
function executeSecureQuery($pdo, $sql, $params = []) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Database query failed: " . $e->getMessage());
        throw new Exception("Database operation failed");
    }
}

// ============================================================================
// AUDIT LOGGING
// ============================================================================

/**
 * Log audit event
 */
function logAuditEvent($user_id, $action, $details, $status = 'success') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, details, status, ip_address, user_agent, created_at) 
                              VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $user_id,
            $action,
            json_encode($details),
            $status,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    } catch (Exception $e) {
        error_log("Audit logging failed: " . $e->getMessage());
    }
}

/**
 * Log security event
 */
function logSecurityEvent($event_type, $details, $severity = 'medium') {
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'type' => $event_type,
        'details' => $details,
        'severity' => $severity,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    error_log("SECURITY: " . json_encode($log_entry));
}

// ============================================================================
// RATE LIMITING
// ============================================================================

/**
 * Check rate limit
 */
function checkRateLimit($identifier, $action, $max_attempts = 5, $time_window = 300) {
    global $pdo;
    
    try {
        // Clean old entries
        $stmt = $pdo->prepare("DELETE FROM rate_limits WHERE created_at < DATE_SUB(NOW(), INTERVAL ? SECOND)");
        $stmt->execute([$time_window]);
        
        // Check current attempts
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM rate_limits 
                              WHERE identifier = ? AND action = ? 
                              AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)");
        $stmt->execute([$identifier, $action, $time_window]);
        
        return $stmt->fetchColumn() < $max_attempts;
    } catch (Exception $e) {
        error_log("Rate limiting check failed: " . $e->getMessage());
        return true; // Allow if rate limiting fails
    }
}

/**
 * Record rate limit attempt
 */
function recordRateLimitAttempt($identifier, $action) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO rate_limits (identifier, action, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$identifier, $action]);
    } catch (Exception $e) {
        error_log("Rate limiting record failed: " . $e->getMessage());
    }
}

// ============================================================================
// SECURITY HEADERS
// ============================================================================

/**
 * Set security headers
 */
function setSecurityHeaders() {
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
    header("X-Frame-Options: DENY");
    header("X-Content-Type-Options: nosniff");
    header("X-XSS-Protection: 1; mode=block");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
    }
}

// ============================================================================
// ERROR HANDLING
// ============================================================================

/**
 * Secure error handler
 */
function secureErrorHandler($errno, $errstr, $errfile, $errline) {
    // Log error internally
    error_log("PHP Error [$errno]: $errstr in $errfile on line $errline");
    
    // Don't display errors to users in production
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
        return true;
    }
    
    // Display errors only in development
    return false;
}

/**
 * Handle exceptions securely
 */
function secureExceptionHandler($exception) {
    // Log exception internally
    error_log("Uncaught Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());
    
    // Show generic error to user
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
        echo "Une erreur s'est produite. Veuillez réessayer plus tard.";
    } else {
        echo "Exception: " . $exception->getMessage();
    }
}

// ============================================================================
// CERTIFICATE SECURITY
// ============================================================================

/**
 * Validate certificate data
 */
function validateCertificateData($data) {
    $errors = [];
    
    if (empty($data['serial_number'])) {
        $errors[] = "Le numéro de série est requis";
    } elseif (!validateCertificateSerial($data['serial_number'])) {
        $errors[] = "Format de numéro de série invalide";
    }
    
    if (empty($data['user_id'])) {
        $errors[] = "L'utilisateur est requis";
    }
    
    if (empty($data['status'])) {
        $errors[] = "Le statut est requis";
    } elseif (!in_array($data['status'], ['active', 'inactive', 'revoked', 'expired'])) {
        $errors[] = "Statut invalide";
    }
    
    return $errors;
}

/**
 * Generate secure certificate ID
 */
function generateCertificateID() {
    return 'CERT-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
}

// ============================================================================
// EMAIL SECURITY
// ============================================================================

/**
 * Send secure email
 */
function sendSecureEmail($to, $subject, $message) {
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: CamGovCA <noreply@camgovca.cm>',
        'Reply-To: support@camgovca.cm',
        'X-Mailer: CamGovCA System'
    ];
    
    return mail($to, $subject, $message, implode("\r\n", $headers));
}

/**
 * Validate email template
 */
function validateEmailTemplate($template, $variables) {
    // Check for required variables
    $required_vars = ['user_name', 'action_url', 'expiry_time'];
    foreach ($required_vars as $var) {
        if (!isset($variables[$var])) {
            throw new Exception("Missing required email variable: $var");
        }
    }
    
    // Sanitize variables
    foreach ($variables as $key => $value) {
        $variables[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
    return $variables;
}

// ============================================================================
// CSRF PROTECTION
// ============================================================================

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
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Add CSRF token to form
 */
function addCSRFTokenToForm() {
    return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}

// ============================================================================
// FILE SECURITY
// ============================================================================

/**
 * Secure file upload directory
 */
function getSecureUploadPath($filename) {
    $upload_dir = dirname(__FILE__) . '/secure_uploads/';
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $unique_name = uniqid() . '_' . time() . '.' . $extension;
    
    return $upload_dir . $unique_name;
}

/**
 * Validate uploaded file
 */
function validateUploadedFile($file) {
    $errors = [];
    
    // Check file size
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        $errors[] = "Le fichier est trop volumineux";
    }
    
    // Check file type
    $allowed_types = ['pem', 'crt', 'cer', 'p12', 'pfx'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $allowed_types)) {
        $errors[] = "Type de fichier non autorisé";
    }
    
    // Check for malicious content
    $content = file_get_contents($file['tmp_name']);
    if (strpos($content, '<?php') !== false || strpos($content, '<script') !== false) {
        $errors[] = "Contenu malveillant détecté";
    }
    
    return $errors;
}

// ============================================================================
// INITIALIZATION
// ============================================================================

/**
 * Initialize security features
 */
function initializeSecurity() {
    // Set error handlers
    set_error_handler('secureErrorHandler');
    set_exception_handler('secureExceptionHandler');
    
    // Configure secure session
    configureSecureSession();
    
    // Set security headers
    setSecurityHeaders();
    
    // Start output buffering
    ob_start();
}

/**
 * Clean up security
 */
function cleanupSecurity() {
    // Flush output buffer
    ob_end_flush();
    
    // Restore error handlers
    restore_error_handler();
    restore_exception_handler();
}

// Initialize security when this file is included
if (!defined('SECURITY_INITIALIZED')) {
    define('SECURITY_INITIALIZED', true);
    initializeSecurity();
}

?> 