<?php
/**
 * Certificate Password Manager
 * Handles all certificate password operations including generation, validation, and security
 */
class CertificatePasswordManager {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Generate a secure certificate password
     */
    public function generatePassword($length = 16) {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        $allChars = $uppercase . $lowercase . $numbers . $special;
        $password = '';
        
        // Ensure at least one character from each category
        $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
        $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
        $password .= $numbers[rand(0, strlen($numbers) - 1)];
        $password .= $special[rand(0, strlen($special) - 1)];
        
        // Fill the rest with random characters
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[rand(0, strlen($allChars) - 1)];
        }
        
        // Shuffle the password to make it more random
        return str_shuffle($password);
    }
    
    /**
     * Validate password strength
     */
    public function validatePasswordStrength($password) {
        $errors = [];
        
        // Get password requirements from settings
        $settings = $this->getPasswordSettings();
        
        if (strlen($password) < $settings['min_length']) {
            $errors[] = "Password must be at least {$settings['min_length']} characters long";
        }
        
        if ($settings['require_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        
        if ($settings['require_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        
        if ($settings['require_numbers'] && !preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }
        
        if ($settings['require_special'] && !preg_match('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $password)) {
            $errors[] = "Password must contain at least one special character";
        }
        
        return $errors;
    }
    
    /**
     * Hash a password
     */
    public function hashPassword($password) {
        return hash('sha256', $password);
    }
    
    /**
     * Set certificate password
     */
    public function setCertificatePassword($certId, $password, $userId, $reason = 'change') {
        try {
            $this->pdo->beginTransaction();
            
            $passwordHash = $this->hashPassword($password);
            
            // Update certificate password
            $stmt = $this->pdo->prepare("
                UPDATE certificates 
                SET certificate_password = ?, 
                    password_hash = ?, 
                    password_updated_at = NOW(),
                    password_attempts = 0,
                    password_locked_until = NULL
                WHERE cert_id = ?
            ");
            $stmt->execute([$password, $passwordHash, $certId]);
            
            // Log password change
            $stmt = $this->pdo->prepare("
                INSERT INTO certificate_password_history 
                (cert_id, password_hash, changed_by, change_reason, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $certId, 
                $passwordHash, 
                $userId, 
                $reason,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
            
            $this->pdo->commit();
            return true;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error setting certificate password: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate certificate password
     */
    public function validateCertificatePassword($certId, $password) {
        try {
            // Check if certificate is locked
            $stmt = $this->pdo->prepare("
                SELECT password_hash, password_locked_until, password_attempts 
                FROM certificates 
                WHERE cert_id = ?
            ");
            $stmt->execute([$certId]);
            $cert = $stmt->fetch();
            
            if (!$cert) {
                return ['success' => false, 'message' => 'Certificate not found'];
            }
            
            // Check if locked
            if ($cert['password_locked_until'] && $cert['password_locked_until'] > date('Y-m-d H:i:s')) {
                return [
                    'success' => false, 
                    'message' => 'Certificate is locked until ' . $cert['password_locked_until']
                ];
            }
            
            // Validate password
            $passwordHash = $this->hashPassword($password);
            
            if ($passwordHash === $cert['password_hash']) {
                // Password correct - reset attempts
                $stmt = $this->pdo->prepare("
                    UPDATE certificates 
                    SET password_attempts = 0, password_locked_until = NULL 
                    WHERE cert_id = ?
                ");
                $stmt->execute([$certId]);
                
                // Log successful attempt
                $this->logPasswordAttempt($certId, true);
                
                return ['success' => true, 'message' => 'Password validated successfully'];
                
            } else {
                // Password incorrect - increment attempts
                $attempts = $cert['password_attempts'] + 1;
                $maxAttempts = $this->getPasswordSettings()['max_attempts'];
                
                if ($attempts >= $maxAttempts) {
                    // Lock certificate
                    $lockoutDuration = $this->getPasswordSettings()['lockout_duration'];
                    $lockedUntil = date('Y-m-d H:i:s', time() + $lockoutDuration);
                    
                    $stmt = $this->pdo->prepare("
                        UPDATE certificates 
                        SET password_attempts = ?, password_locked_until = ? 
                        WHERE cert_id = ?
                    ");
                    $stmt->execute([$attempts, $lockedUntil, $certId]);
                    
                    // Log failed attempt
                    $this->logPasswordAttempt($certId, false);
                    
                    return [
                        'success' => false, 
                        'message' => "Certificate locked due to too many failed attempts. Locked until $lockedUntil"
                    ];
                } else {
                    // Update attempts
                    $stmt = $this->pdo->prepare("
                        UPDATE certificates 
                        SET password_attempts = ? 
                        WHERE cert_id = ?
                    ");
                    $stmt->execute([$attempts, $certId]);
                    
                    // Log failed attempt
                    $this->logPasswordAttempt($certId, false);
                    
                    $remaining = $maxAttempts - $attempts;
                    return [
                        'success' => false, 
                        'message' => "Invalid password. $remaining attempts remaining"
                    ];
                }
            }
            
        } catch (Exception $e) {
            error_log("Error validating certificate password: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error validating password'];
        }
    }
    
    /**
     * Get password settings from database
     */
    private function getPasswordSettings() {
        $stmt = $this->pdo->prepare("
            SELECT setting_key, setting_value 
            FROM system_settings 
            WHERE setting_key LIKE 'certificate_password_%'
        ");
        $stmt->execute();
        $settings = [];
        
        while ($row = $stmt->fetch()) {
            $key = str_replace('certificate_password_', '', $row['setting_key']);
            $value = $row['setting_value'];
            
            // Convert boolean strings to actual booleans
            if ($value === 'true') $value = true;
            elseif ($value === 'false') $value = false;
            elseif (is_numeric($value)) $value = (int)$value;
            
            $settings[$key] = $value;
        }
        
        // Set defaults if not found
        $defaults = [
            'min_length' => 8,
            'require_uppercase' => true,
            'require_lowercase' => true,
            'require_numbers' => true,
            'require_special' => true,
            'max_attempts' => 5,
            'lockout_duration' => 1800,
            'expiry_days' => 365
        ];
        
        return array_merge($defaults, $settings);
    }
    
    /**
     * Log password attempt
     */
    private function logPasswordAttempt($certId, $success) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO certificate_password_attempts 
                (cert_id, ip_address, success, user_agent)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $certId,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $success,
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        } catch (Exception $e) {
            error_log("Error logging password attempt: " . $e->getMessage());
        }
    }
    
    /**
     * Get certificate password requirements for display
     */
    public function getPasswordRequirements() {
        $settings = $this->getPasswordSettings();
        
        $requirements = [
            "Minimum {$settings['min_length']} characters"
        ];
        
        if ($settings['require_uppercase']) {
            $requirements[] = "At least one uppercase letter (A-Z)";
        }
        
        if ($settings['require_lowercase']) {
            $requirements[] = "At least one lowercase letter (a-z)";
        }
        
        if ($settings['require_numbers']) {
            $requirements[] = "At least one number (0-9)";
        }
        
        if ($settings['require_special']) {
            $requirements[] = "At least one special character (!@#$%^&*)";
        }
        
        return $requirements;
    }
    
    /**
     * Check if certificate password is expired
     */
    public function isPasswordExpired($certId) {
        $settings = $this->getPasswordSettings();
        $expiryDays = $settings['expiry_days'];
        
        $stmt = $this->pdo->prepare("
            SELECT password_updated_at 
            FROM certificates 
            WHERE cert_id = ?
        ");
        $stmt->execute([$certId]);
        $cert = $stmt->fetch();
        
        if (!$cert || !$cert['password_updated_at']) {
            return true; // No password set, consider expired
        }
        
        $passwordDate = new DateTime($cert['password_updated_at']);
        $now = new DateTime();
        $diff = $now->diff($passwordDate);
        
        return $diff->days > $expiryDays;
    }
    
    /**
     * Reset certificate password (admin function)
     */
    public function resetCertificatePassword($certId, $adminUserId) {
        $newPassword = $this->generatePassword();
        return $this->setCertificatePassword($certId, $newPassword, $adminUserId, 'admin_reset');
    }
}
?> 