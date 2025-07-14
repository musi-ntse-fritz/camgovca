<?php
/**
 * CamGovCA 2FA Verification System
 * Centralized 2FA verification for all critical operations
 */

require_once '2fa_manager.php';
require_once 'email_service.php';
require_once 'audit_logger.php';

class TwoFactorVerification {
    private $pdo;
    private $logger;
    private $emailService;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->logger = getAuditLogger();
        $this->emailService = new EmailService('resend');
    }
    
    /**
     * Check if 2FA is required for an operation
     */
    public function is2FARequired($operation_type, $user_id = null) {
        // Get 2FA settings
        $settings = $this->get2FASettings();
        
        if (!$settings['enabled']) {
            return false;
        }
        
        // Always require 2FA for admin operations
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            return true;
        }
        
        // Check operation-specific requirements
        $critical_operations = [
            'login',
            'certificate_revoke',
            'certificate_suspend',
            'certificate_resume',
            'password_change',
            'user_delete',
            'organization_delete',
            'backup_restore',
            'system_settings',
            'bulk_operations'
        ];
        
        return in_array($operation_type, $critical_operations);
    }
    
    /**
     * Generate and send 2FA code for operation
     */
    public function generate2FACode($operation_type, $user_id = null, $email = null) {
        try {
            $user_id = $user_id ?: ($_SESSION['user_id'] ?? $_SESSION['admin_user_id']);
            
            // Get email from parameter, session, or database
            if (!$email) {
                $email = $_SESSION['user_email'] ?? $_SESSION['admin_email'] ?? null;
                
                // If still no email, get it from database
                if (!$email && $user_id) {
                    $stmt = $this->pdo->prepare("SELECT email FROM users WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                    $user = $stmt->fetch();
                    $email = $user['email'] ?? null;
                }
            }
            
            if (!$user_id || !$email) {
                return ['success' => false, 'message' => 'User information not available'];
            }
            
            // Generate OTP
            $otp = $this->generateOTP();
            $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            
            // Store OTP in database
            $stmt = $this->pdo->prepare("
                INSERT INTO two_factor_codes (user_id, code, operation_type, expires_at, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$user_id, $otp, $operation_type, $expires_at]);
            
            // Send OTP via email
            $subject = "CamGovCA - Code de vérification pour $operation_type";
            $message = "
                <h2>Code de vérification CamGovCA</h2>
                <p>Votre code de vérification pour l'opération <strong>$operation_type</strong> est :</p>
                <h1 style='font-size: 32px; color: #007bff; text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px;'>$otp</h1>
                <p><strong>Ce code expire dans 10 minutes.</strong></p>
                <p>Si vous n'avez pas demandé ce code, veuillez ignorer cet email.</p>
            ";
            
            $emailResult = $this->emailService->sendEmail($email, $subject, $message);
            
            if ($emailResult['success']) {
                $this->logger->logSecurityEvent('2fa_code_sent', [
                    'user_id' => $user_id,
                    'operation_type' => $operation_type,
                    'email' => $email
                ]);
                
                $message_text = 'Code de vérification envoyé';
                if (isset($emailResult['logged']) && $emailResult['logged']) {
                    $message_text .= ' (logged for development - check logs/email_log.txt or view_email_log.php)';
                }
                
                return [
                    'success' => true,
                    'message' => $message_text,
                    'expires_at' => $expires_at,
                    'logged' => $emailResult['logged'] ?? false,
                    'code' => $otp
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi du code: ' . $emailResult['message']
                ];
            }
            
        } catch (Exception $e) {
            error_log("2FA code generation error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la génération du code'];
        }
    }
    
    /**
     * Verify 2FA code for operation
     */
    public function verify2FACode($code, $operation_type, $user_id = null) {
        try {
            $user_id = $user_id ?: ($_SESSION['user_id'] ?? $_SESSION['admin_user_id']);
            
            if (!$user_id) {
                return ['success' => false, 'message' => 'User information not available'];
            }
            
            // Get the most recent valid code
            $stmt = $this->pdo->prepare("
                SELECT * FROM two_factor_codes 
                WHERE user_id = ? AND operation_type = ? AND expires_at > NOW() AND used = 0
                ORDER BY created_at DESC 
                LIMIT 1
            ");
            $stmt->execute([$user_id, $operation_type]);
            $codeRecord = $stmt->fetch();
            
            if (!$codeRecord) {
                $this->logger->logSecurityEvent('2fa_code_invalid', [
                    'user_id' => $user_id,
                    'operation_type' => $operation_type,
                    'provided_code' => $code
                ]);
                return ['success' => false, 'message' => 'Code invalide ou expiré'];
            }
            
            if ($codeRecord['code'] !== $code) {
                $this->logger->logSecurityEvent('2fa_code_mismatch', [
                    'user_id' => $user_id,
                    'operation_type' => $operation_type,
                    'provided_code' => $code,
                    'expected_code' => $codeRecord['code']
                ]);
                return ['success' => false, 'message' => 'Code incorrect'];
            }
            
            // Mark code as used
            $stmt = $this->pdo->prepare("
                UPDATE two_factor_codes SET used = 1, used_at = NOW() WHERE id = ?
            ");
            $stmt->execute([$codeRecord['id']]);
            
            // Log successful verification
            $this->logger->logSecurityEvent('2fa_verification_success', [
                'user_id' => $user_id,
                'operation_type' => $operation_type
            ]);
            
            return ['success' => true, 'message' => 'Code vérifié avec succès'];
            
        } catch (Exception $e) {
            error_log("2FA verification error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la vérification'];
        }
    }
    
    /**
     * Generate 6-digit OTP
     */
    private function generateOTP() {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get 2FA settings
     */
    private function get2FASettings() {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM two_factor_settings WHERE id = 1");
            $stmt->execute();
            $settings = $stmt->fetch();
            
            return $settings ?: [
                'enabled' => true,
                'email_enabled' => true,
                'sms_enabled' => false,
                'backup_codes_enabled' => true,
                'max_attempts' => 3,
                'lockout_duration' => 15
            ];
        } catch (Exception $e) {
            return ['enabled' => true];
        }
    }
}

/**
 * Helper function to get 2FA verification instance
 */
function get2FAVerification($pdo = null) {
    if (!$pdo) {
        require_once 'config/database.php';
        $pdo = getDBConnection();
    }
    return new TwoFactorVerification($pdo);
}

/**
 * Check if current operation requires 2FA
 */
function require2FAForOperation($operation_type) {
    $verification = get2FAVerification();
    return $verification->is2FARequired($operation_type);
}

/**
 * Generate 2FA code for operation
 */
function generate2FACodeForOperation($operation_type, $user_id = null, $email = null) {
    $verification = get2FAVerification();
    return $verification->generate2FACode($operation_type, $user_id, $email);
}

/**
 * Verify 2FA code for operation
 */
function verify2FACodeForOperation($code, $operation_type, $user_id = null) {
    $verification = get2FAVerification();
    return $verification->verify2FACode($code, $operation_type, $user_id);
}
?> 