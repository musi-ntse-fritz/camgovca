<?php
/**
 * CamGovCA 2FA Manager
 * Handles OTP-based Two-Factor Authentication
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/audit_logger.php';
require_once __DIR__ . '/email_service.php';

class TwoFactorAuth {
    private $pdo;
    private $auditLogger;
    
    // OTP Configuration
    private $otp_length = 6;
    private $otp_expiry = 300; // 5 minutes
    private $max_attempts = 3;
    private $lockout_duration = 900; // 15 minutes
    
    public function __construct($pdo = null) {
        $this->pdo = $pdo ?: getDBConnection();
        $this->auditLogger = new AuditLogger($this->pdo);
    }
    
    /**
     * Generate a new OTP for a user
     */
    public function generateOTP($user_id, $purpose = 'login') {
        try {
            // Check if user is locked
            if ($this->isUserLocked($user_id)) {
                return [
                    'success' => false,
                    'message' => 'Account is temporarily locked due to too many failed attempts'
                ];
            }
            
            // Generate OTP
            $otp = $this->generateRandomOTP();
            $expires_at = date('Y-m-d H:i:s', time() + $this->otp_expiry);
            
            // Store OTP in database
            $stmt = $this->pdo->prepare("
                INSERT INTO two_factor_otps 
                (user_id, otp_hash, purpose, expires_at, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            $otp_hash = password_hash($otp, PASSWORD_DEFAULT);
            $stmt->execute([$user_id, $otp_hash, $purpose, $expires_at]);
            
            // Log OTP generation
            $this->auditLogger->logSecurityEvent('2fa_otp_generated', [
                'user_id' => $user_id,
                'purpose' => $purpose,
                'expires_at' => $expires_at
            ]);
            
            return [
                'success' => true,
                'otp' => $otp,
                'expires_at' => $expires_at,
                'message' => 'OTP generated successfully'
            ];
            
        } catch (Exception $e) {
            error_log("Error generating OTP: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error generating OTP'
            ];
        }
    }
    
    /**
     * Validate OTP for a user
     */
    public function validateOTP($user_id, $otp, $purpose = 'login') {
        try {
            // Check if user is locked
            if ($this->isUserLocked($user_id)) {
                return [
                    'success' => false,
                    'message' => 'Account is temporarily locked due to too many failed attempts'
                ];
            }
            
            // Get valid OTP from database
            $stmt = $this->pdo->prepare("
                SELECT otp_id, otp_hash, expires_at, attempts 
                FROM two_factor_otps 
                WHERE user_id = ? AND purpose = ? AND expires_at > NOW() 
                AND attempts < ? 
                ORDER BY created_at DESC 
                LIMIT 1
            ");
            $stmt->execute([$user_id, $purpose, $this->max_attempts]);
            $otp_record = $stmt->fetch();
            
            if (!$otp_record) {
                $this->incrementFailedAttempts($user_id);
                $this->auditLogger->logSecurityEvent('2fa_otp_invalid', [
                    'user_id' => $user_id,
                    'reason' => 'No valid OTP found or expired'
                ]);
                return [
                    'success' => false,
                    'message' => 'Invalid or expired OTP'
                ];
            }
            
            // Check if OTP is correct
            if (password_verify($otp, $otp_record['otp_hash'])) {
                // Mark OTP as used
                $stmt = $this->pdo->prepare("
                    UPDATE two_factor_otps 
                    SET used_at = NOW(), attempts = attempts + 1 
                    WHERE otp_id = ?
                ");
                $stmt->execute([$otp_record['otp_id']]);
                
                // Reset failed attempts
                $this->resetFailedAttempts($user_id);
                
                // Log successful validation
                $this->auditLogger->logSecurityEvent('2fa_otp_validated', [
                    'user_id' => $user_id,
                    'purpose' => $purpose
                ]);
                
                return [
                    'success' => true,
                    'message' => 'OTP validated successfully'
                ];
            } else {
                // Increment attempts
                $stmt = $this->pdo->prepare("
                    UPDATE two_factor_otps 
                    SET attempts = attempts + 1 
                    WHERE otp_id = ?
                ");
                $stmt->execute([$otp_record['otp_id']]);
                
                $this->incrementFailedAttempts($user_id);
                
                // Log failed attempt
                $this->auditLogger->logSecurityEvent('2fa_otp_failed', [
                    'user_id' => $user_id,
                    'purpose' => $purpose,
                    'attempts' => $otp_record['attempts'] + 1
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Invalid OTP'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Error validating OTP: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error validating OTP'
            ];
        }
    }
    
    /**
     * Check if user is locked due to too many failed attempts
     */
    private function isUserLocked($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT locked_until 
            FROM two_factor_lockouts 
            WHERE user_id = ? AND locked_until > NOW()
        ");
        $stmt->execute([$user_id]);
        $lockout = $stmt->fetch();
        
        return $lockout !== false;
    }
    
    /**
     * Increment failed attempts and lock account if necessary
     */
    private function incrementFailedAttempts($user_id) {
        $stmt = $this->pdo->prepare("
            INSERT INTO two_factor_lockouts (user_id, failed_attempts, locked_until) 
            VALUES (?, 1, DATE_ADD(NOW(), INTERVAL ? SECOND))
            ON DUPLICATE KEY UPDATE 
            failed_attempts = failed_attempts + 1,
            locked_until = CASE 
                WHEN failed_attempts >= ? THEN DATE_ADD(NOW(), INTERVAL ? SECOND)
                ELSE locked_until 
            END
        ");
        $stmt->execute([$user_id, $this->lockout_duration, $this->max_attempts, $this->lockout_duration]);
    }
    
    /**
     * Reset failed attempts for a user
     */
    private function resetFailedAttempts($user_id) {
        $stmt = $this->pdo->prepare("
            DELETE FROM two_factor_lockouts 
            WHERE user_id = ?
        ");
        $stmt->execute([$user_id]);
    }
    
    /**
     * Generate random OTP
     */
    private function generateRandomOTP() {
        $otp = '';
        for ($i = 0; $i < $this->otp_length; $i++) {
            $otp .= mt_rand(0, 9);
        }
        return $otp;
    }
    
    /**
     * Send OTP via email (placeholder for email integration)
     */
    public function sendOTPEmail($user_id, $otp) {
        try {
            // Get user email
            $stmt = $this->pdo->prepare("SELECT email, first_name, last_name FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            // Use the new email service
            $emailService = getEmailService();
            $user_name = $user['first_name'] . ' ' . $user['last_name'];
            
            $result = $emailService->sendOTPEmail($user['email'], $otp, $user_name);
            
            if ($result['success']) {
                $this->auditLogger->logSecurityEvent('2fa_email_sent', [
                    'user_id' => $user_id,
                    'email' => $user['email'],
                    'service' => 'email'
                ]);
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error sending OTP email: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error sending email: ' . $e->getMessage()];
        }
    }
    
    /**
     * Send OTP via SMS (placeholder for SMS integration)
     */
    public function sendOTPSMS($user_id, $otp) {
        try {
            // Get user phone
            $stmt = $this->pdo->prepare("SELECT phone, first_name, last_name FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if (!$user || !$user['phone']) {
                return ['success' => false, 'message' => 'User phone number not found'];
            }
            
            // SMS content
            $message = "CamGovCA: Your 2FA code is {$otp}. Valid for 5 minutes.";
            
            // Placeholder for SMS API integration
            // You would integrate with services like Twilio, AfricasTalking, etc.
            $sms_sent = true; // Placeholder
            
            if ($sms_sent) {
                $this->auditLogger->logSecurityEvent('2fa_sms_sent', [
                    'user_id' => $user_id,
                    'phone' => $user['phone']
                ]);
                return ['success' => true, 'message' => 'OTP sent via SMS'];
            } else {
                return ['success' => false, 'message' => 'Failed to send SMS'];
            }
            
        } catch (Exception $e) {
            error_log("Error sending OTP SMS: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error sending SMS'];
        }
    }
    
    /**
     * Get user's 2FA settings
     */
    public function getUser2FASettings($user_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT two_factor_enabled, two_factor_method, backup_codes 
                FROM users 
                WHERE user_id = ?
            ");
            $stmt->execute([$user_id]);
            $settings = $stmt->fetch();
            
            return [
                'enabled' => $settings['two_factor_enabled'] ?? false,
                'method' => $settings['two_factor_method'] ?? 'email',
                'backup_codes' => $settings['backup_codes'] ? json_decode($settings['backup_codes'], true) : []
            ];
        } catch (Exception $e) {
            error_log("Error getting 2FA settings: " . $e->getMessage());
            return ['enabled' => false, 'method' => 'email', 'backup_codes' => []];
        }
    }
    
    /**
     * Enable 2FA for a user
     */
    public function enable2FA($user_id, $method = 'email') {
        try {
            // Generate backup codes
            $backup_codes = $this->generateBackupCodes();
            
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET two_factor_enabled = 1, two_factor_method = ?, backup_codes = ? 
                WHERE user_id = ?
            ");
            $stmt->execute([$method, json_encode($backup_codes), $user_id]);
            
            $this->auditLogger->logSecurityEvent('2fa_enabled', [
                'user_id' => $user_id,
                'method' => $method
            ]);
            
            return [
                'success' => true,
                'backup_codes' => $backup_codes,
                'message' => '2FA enabled successfully'
            ];
            
        } catch (Exception $e) {
            error_log("Error enabling 2FA: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error enabling 2FA'];
        }
    }
    
    /**
     * Disable 2FA for a user
     */
    public function disable2FA($user_id) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET two_factor_enabled = 0, two_factor_method = NULL, backup_codes = NULL 
                WHERE user_id = ?
            ");
            $stmt->execute([$user_id]);
            
            $this->auditLogger->logSecurityEvent('2fa_disabled', [
                'user_id' => $user_id
            ]);
            
            return ['success' => true, 'message' => '2FA disabled successfully'];
            
        } catch (Exception $e) {
            error_log("Error disabling 2FA: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error disabling 2FA'];
        }
    }
    
    /**
     * Generate backup codes
     */
    private function generateBackupCodes() {
        $codes = [];
        for ($i = 0; $i < 10; $i++) {
            $codes[] = strtoupper(substr(md5(uniqid()), 0, 8));
        }
        return $codes;
    }
    
    /**
     * Validate backup code
     */
    public function validateBackupCode($user_id, $backup_code) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT backup_codes 
                FROM users 
                WHERE user_id = ?
            ");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if (!$user || !$user['backup_codes']) {
                return ['success' => false, 'message' => 'No backup codes found'];
            }
            
            $backup_codes = json_decode($user['backup_codes'], true);
            
            if (in_array(strtoupper($backup_code), $backup_codes)) {
                // Remove used backup code
                $backup_codes = array_diff($backup_codes, [strtoupper($backup_code)]);
                
                $stmt = $this->pdo->prepare("
                    UPDATE users 
                    SET backup_codes = ? 
                    WHERE user_id = ?
                ");
                $stmt->execute([json_encode(array_values($backup_codes)), $user_id]);
                
                $this->auditLogger->logSecurityEvent('2fa_backup_code_used', [
                    'user_id' => $user_id
                ]);
                
                return ['success' => true, 'message' => 'Backup code validated'];
            } else {
                return ['success' => false, 'message' => 'Invalid backup code'];
            }
            
        } catch (Exception $e) {
            error_log("Error validating backup code: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error validating backup code'];
        }
    }
    
    /**
     * Clean up expired OTPs
     */
    public function cleanupExpiredOTPs() {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM two_factor_otps 
                WHERE expires_at < NOW()
            ");
            $stmt->execute();
            
            $deleted_count = $stmt->rowCount();
            
            if ($deleted_count > 0) {
                $this->auditLogger->logSecurityEvent('2fa_cleanup', [
                    'deleted_count' => $deleted_count
                ]);
            }
            
            return $deleted_count;
            
        } catch (Exception $e) {
            error_log("Error cleaning up expired OTPs: " . $e->getMessage());
            return 0;
        }
    }
}

/**
 * Global 2FA instance
 */
$twoFactorAuth = null;

/**
 * Get or create 2FA instance
 */
function getTwoFactorAuth() {
    global $twoFactorAuth;
    if (!$twoFactorAuth) {
        $twoFactorAuth = new TwoFactorAuth();
    }
    return $twoFactorAuth;
}
?> 