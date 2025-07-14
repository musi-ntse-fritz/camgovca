<?php
require_once __DIR__ . '/../config/database.php';

// User authentication
function authenticateUser($username, $password) {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    $stmt = $pdo->prepare("SELECT user_id, username, password_hash, user_type, status FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash']) && $user['status'] === 'active') {
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
        $stmt->execute([$user['user_id']]);
        return $user;
    }
    return false;
}

// Certificate lookup
function getCertificateBySerial($serial_number) {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    $stmt = $pdo->prepare("SELECT c.*, u.first_name, u.last_name, u.email, ca.ca_name FROM certificates c JOIN users u ON c.user_id = u.user_id JOIN certificate_authorities ca ON c.ca_id = ca.ca_id WHERE c.serial_number = ?");
    $stmt->execute([$serial_number]);
    return $stmt->fetch();
}

// FAQ retrieval
function getFAQByCategory($category, $language = 'fr') {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    $stmt = $pdo->prepare("SELECT * FROM faq_entries WHERE category = ? AND language = ? AND status = 'active' ORDER BY sort_order ASC");
    $stmt->execute([$category, $language]);
    return $stmt->fetchAll();
}

// System settings
function getSystemSetting($key) {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    $stmt = $pdo->prepare("SELECT setting_value, setting_type FROM system_settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $setting = $stmt->fetch();
    if ($setting) {
        switch ($setting['setting_type']) {
            case 'integer': return (int)$setting['setting_value'];
            case 'boolean': return (bool)$setting['setting_value'];
            case 'json': return json_decode($setting['setting_value'], true);
            default: return $setting['setting_value'];
        }
    }
    return false;
} 