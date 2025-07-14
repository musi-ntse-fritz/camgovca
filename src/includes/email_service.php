<?php
/**
 * CamGovCA Email Service
 * Supports multiple free email APIs for 2FA and notifications
 */

class EmailService {
    private $api_key;
    private $from_email;
    private $from_name;
    private $service;
    
    public function __construct($service = 'resend', $api_key = null, $from_email = null, $from_name = null) {
        $this->service = $service;
        $this->api_key = $api_key ?: $this->getDefaultApiKey($service);
        $this->from_email = $from_email ?: $this->getDefaultFromEmail($service);
        $this->from_name = $from_name ?: 'CamGovCA';
    }
    
    /**
     * Get default API key from environment or config
     */
    private function getDefaultApiKey($service) {
        // Check environment variables first
        $env_key = strtoupper($service) . '_API_KEY';
        if (getenv($env_key)) {
            return getenv($env_key);
        }
        
        // Check config file
        $config_file = __DIR__ . '/../config/email_config.php';
        if (file_exists($config_file)) {
            $config = include $config_file;
            return $config[$service]['api_key'] ?? null;
        }
        
        return null;
    }
    
    /**
     * Get default from email
     */
    private function getDefaultFromEmail($service) {
        switch ($service) {
            case 'resend':
                return 'onboarding@resend.dev'; // Use Resend's verified domain for testing
            case 'mailgun':
                return 'noreply@camgovca.cm';
            case 'sendgrid':
                return 'noreply@camgovca.cm';
            default:
                return 'noreply@camgovca.cm';
        }
    }
    
    /**
     * Send email using the configured service
     */
    public function sendEmail($to_email, $subject, $html_content, $text_content = null) {
        $services_to_try = [$this->service];
        
        // Add fallback services if configured
        $config_file = __DIR__ . '/../config/email_config.php';
        if (file_exists($config_file)) {
            $config = include $config_file;
            if (isset($config['fallback_service']) && $config['fallback_service'] !== $this->service) {
                $services_to_try[] = $config['fallback_service'];
            }
        }
        
        // Always try local as final fallback
        if (!in_array('local', $services_to_try)) {
            $services_to_try[] = 'local';
        }
        
        foreach ($services_to_try as $service) {
            $result = $this->sendViaService($service, $to_email, $subject, $html_content, $text_content);
            if ($result['success']) {
                return $result;
            }
        }
        
        // If all services fail, log the email
        return $this->logEmailForDevelopment($to_email, $subject, $html_content, $text_content);
    }
    
    /**
     * Send via specific service
     */
    private function sendViaService($service, $to_email, $subject, $html_content, $text_content = null) {
        switch ($service) {
            case 'resend':
                return $this->sendViaResend($to_email, $subject, $html_content, $text_content);
            case 'mailgun':
                return $this->sendViaMailgun($to_email, $subject, $html_content, $text_content);
            case 'sendgrid':
                return $this->sendViaSendGrid($to_email, $subject, $html_content, $text_content);
            case 'local':
                return $this->sendViaLocal($to_email, $subject, $html_content, $text_content);
            default:
                return ['success' => false, 'message' => 'Unknown email service: ' . $service];
        }
    }
    
    /**
     * Send via Resend (3,000 free emails/month)
     */
    private function sendViaResend($to_email, $subject, $html_content, $text_content = null) {
        if (!$this->api_key) {
            return ['success' => false, 'message' => 'Resend API key not configured'];
        }
        
        // Check if this email can be sent via Resend free tier
        $config_file = __DIR__ . '/../config/email_config.php';
        $config = include $config_file;
        $allowed_test_emails = $config['resend']['allowed_test_emails'] ?? ['fritzntse@gmail.com'];
        
        // If email is not in allowed list, use fallback
        if (!in_array($to_email, $allowed_test_emails)) {
            // Log the attempt and use fallback
            $this->logEmailForDevelopment($to_email, $subject, $html_content, $text_content);
            
            // Send notification to admin about the attempt
            if (isset($config['admin_notification_email'])) {
                $admin_notification = "
                <h3>Email Delivery Notice</h3>
                <p>An email was attempted to be sent to: <strong>$to_email</strong></p>
                <p>Subject: $subject</p>
                <p>This email was logged locally due to Resend free tier limitations.</p>
                <p>To enable email delivery to all users, please:</p>
                <ol>
                    <li>Verify your domain at <a href='https://resend.com/domains'>resend.com/domains</a></li>
                    <li>Update the from_email to use your verified domain</li>
                    <li>Or upgrade to a paid Resend plan</li>
                </ol>
                ";
                
                $this->sendViaResend($config['admin_notification_email'], 'Email Delivery Notice - CamGovCA', $admin_notification);
            }
            
            return [
                'success' => true, 
                'message' => 'Email logged locally (Resend free tier limitation). Check logs/email_log.txt',
                'logged' => true,
                'reason' => 'free_tier_limit'
            ];
        }
        
        // Use Resend's default domain for sending emails
        $data = [
            'from' => $this->from_name . ' <' . $this->from_email . '>',
            'to' => [$to_email],
            'subject' => $subject,
            'html' => $html_content
        ];
        
        if ($text_content) {
            $data['text'] = $text_content;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.resend.com/emails');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        if ($curl_error) {
            return ['success' => false, 'message' => 'CURL error: ' . $curl_error];
        }
        
        if ($http_code === 200) {
            // Also log the email for debugging
            $this->logEmailForDevelopment($to_email, $subject, $html_content, $text_content);
            return ['success' => true, 'message' => 'Email sent via Resend'];
        } else {
            $error = json_decode($response, true);
            $error_message = $error['message'] ?? 'Unknown error (HTTP ' . $http_code . ')';
            
            // Log the error for debugging
            error_log("Resend API error: HTTP $http_code - $error_message");
            
            return ['success' => false, 'message' => 'Resend error: ' . $error_message];
        }
    }
    
    /**
     * Get custom domain from config
     */
    private function getCustomDomain() {
        $config_file = __DIR__ . '/../config/email_config.php';
        if (file_exists($config_file)) {
            $config = include $config_file;
            return $config['resend']['domain'] ?? null;
        }
        return null;
    }
    
    /**
     * Send via Mailgun (5,000 free emails/month)
     */
    private function sendViaMailgun($to_email, $subject, $html_content, $text_content = null) {
        if (!$this->api_key) {
            return ['success' => false, 'message' => 'Mailgun API key not configured'];
        }
        
        $domain = 'camgovca.cm'; // You'll need to configure this
        $data = [
            'from' => $this->from_name . ' <' . $this->from_email . '>',
            'to' => $to_email,
            'subject' => $subject,
            'html' => $html_content
        ];
        
        if ($text_content) {
            $data['text'] = $text_content;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.mailgun.net/v3/{$domain}/messages");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_USERPWD, "api:{$this->api_key}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200) {
            return ['success' => true, 'message' => 'Email sent via Mailgun'];
        } else {
            return ['success' => false, 'message' => 'Mailgun error: HTTP ' . $http_code];
        }
    }
    
    /**
     * Send via SendGrid (100 free emails/day)
     */
    private function sendViaSendGrid($to_email, $subject, $html_content, $text_content = null) {
        if (!$this->api_key) {
            return ['success' => false, 'message' => 'SendGrid API key not configured'];
        }
        
        $data = [
            'personalizations' => [
                [
                    'to' => [['email' => $to_email]]
                ]
            ],
            'from' => ['email' => $this->from_email, 'name' => $this->from_name],
            'subject' => $subject,
            'content' => [
                ['type' => 'text/html', 'value' => $html_content]
            ]
        ];
        
        if ($text_content) {
            $data['content'][] = ['type' => 'text/plain', 'value' => $text_content];
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 202) {
            return ['success' => true, 'message' => 'Email sent via SendGrid'];
        } else {
            return ['success' => false, 'message' => 'SendGrid error: HTTP ' . $http_code];
        }
    }
    
    /**
     * Send via Local mail server
     */
    private function sendViaLocal($to_email, $subject, $html_content, $text_content = null) {
        // Always log emails in development mode
        $config_file = __DIR__ . '/../config/email_config.php';
        if (file_exists($config_file)) {
            $config = include $config_file;
            if (isset($config['development_mode']) && $config['development_mode']) {
                $log_result = $this->logEmailForDevelopment($to_email, $subject, $html_content, $text_content);
                return $log_result;
            }
        }
        
        // Check if we're in a development environment without mail server
        if (!$this->isMailServerAvailable()) {
            // Log the email instead of sending it
            return $this->logEmailForDevelopment($to_email, $subject, $html_content, $text_content);
        }
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->from_name . ' <' . $this->from_email . '>',
            'Reply-To: ' . $this->from_email,
            'X-Mailer: CamGovCA Email Service'
        ];
        
        $mail_sent = mail($to_email, $subject, $html_content, implode("\r\n", $headers));
        
        if ($mail_sent) {
            // Also log successful emails if configured
            if (file_exists($config_file)) {
                $config = include $config_file;
                if (isset($config['always_log']) && $config['always_log']) {
                    $this->logEmailForDevelopment($to_email, $subject, $html_content, $text_content);
                }
            }
            return ['success' => true, 'message' => 'Email sent via local mail server'];
        } else {
            return ['success' => false, 'message' => 'Failed to send email via local mail server'];
        }
    }
    
    /**
     * Check if mail server is available
     */
    private function isMailServerAvailable() {
        // Try to connect to localhost:25
        $connection = @fsockopen('localhost', 25, $errno, $errstr, 5);
        if ($connection) {
            fclose($connection);
            return true;
        }
        return false;
    }
    
    /**
     * Log email for development environment
     */
    private function logEmailForDevelopment($to_email, $subject, $html_content, $text_content = null) {
        $log_dir = __DIR__ . '/../logs';
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $log_file = $log_dir . '/email_log.txt';
        $timestamp = date('Y-m-d H:i:s');
        
        $log_entry = "=== EMAIL LOG ENTRY ===\n";
        $log_entry .= "Timestamp: $timestamp\n";
        $log_entry .= "To: $to_email\n";
        $log_entry .= "Subject: $subject\n";
        $log_entry .= "From: {$this->from_name} <{$this->from_email}>\n";
        $log_entry .= "Content:\n$html_content\n";
        if ($text_content) {
            $log_entry .= "Text Content:\n$text_content\n";
        }
        $log_entry .= "=== END EMAIL LOG ===\n\n";
        
        file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
        
        return [
            'success' => true, 
            'message' => 'Email logged for development (no mail server available). Check logs/email_log.txt',
            'logged' => true
        ];
    }
    
    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail($to_email, $user_name, $reset_link) {
        $subject = "CamGovCA - Password Reset Request";
        
        $html_content = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>CamGovCA Password Reset</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #667eea; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
                .reset-button { display: inline-block; background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 10px; font-weight: bold; margin: 20px 0; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>🔐 CamGovCA</h1>
                <p>Password Reset Request</p>
            </div>
            <div class='content'>
                <h2>Hello {$user_name},</h2>
                <p>We received a request to reset your password for your CamGovCA account.</p>
                <p>Click the button below to reset your password:</p>
                <div style='text-align: center;'>
                    <a href='{$reset_link}' class='reset-button'>Reset Password</a>
                </div>
                <div class='warning'>
                    <strong>⚠️ Important:</strong> This link will expire in 24 hours. 
                    If you didn't request this password reset, please ignore this email.
                </div>
                <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
                <p style='word-break: break-all; background: #f1f3f4; padding: 10px; border-radius: 5px;'>{$reset_link}</p>
                <p>Best regards,<br><strong>CamGovCA Team</strong></p>
            </div>
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
                <p>© 2025 CamGovCA - Cameroon Government Certificate Authority</p>
            </div>
        </body>
        </html>
        ";
        
        $text_content = "
        CamGovCA Password Reset Request
        
        Hello {$user_name},
        
        We received a request to reset your password for your CamGovCA account.
        
        To reset your password, visit this link:
        {$reset_link}
        
        This link will expire in 24 hours.
        
        If you didn't request this password reset, please ignore this email.
        
        Best regards,
        CamGovCA Team
        
        ---
        This is an automated message. Please do not reply to this email.
        © 2025 CamGovCA - Cameroon Government Certificate Authority
        ";
        
        return $this->sendEmail($to_email, $subject, $html_content, $text_content);
    }
    
    /**
     * Send 2FA OTP email
     */
    public function sendOTPEmail($to_email, $otp, $user_name = 'User', $expires_in = '5 minutes') {
        $subject = "CamGovCA - Two-Factor Authentication Code";
        
        $html_content = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>CamGovCA 2FA Code</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #667eea; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
                .otp-code { background: #667eea; color: white; font-size: 32px; font-weight: bold; text-align: center; padding: 20px; border-radius: 10px; margin: 20px 0; letter-spacing: 5px; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>🔐 CamGovCA</h1>
                <p>Two-Factor Authentication</p>
            </div>
            <div class='content'>
                <h2>Hello {$user_name},</h2>
                <p>Your two-factor authentication code is:</p>
                <div class='otp-code'>{$otp}</div>
                <div class='warning'>
                    <strong>⚠️ SECURITY WARNING:</strong>
                    <ul style='margin: 10px 0; padding-left: 20px;'>
                        <li>This code will expire in {$expires_in}</li>
                        <li><strong>NEVER share this code with anyone</strong> - CamGovCA staff will never ask for your OTP code</li>
                        <li>If you didn't request this code, <strong>DO NOT enter it</strong> and contact support immediately</li>
                        <li>This code is for your CamGovCA account login only</li>
                    </ul>
                </div>
                <p>This code is required to complete your login to the CamGovCA system.</p>
                <p>Best regards,<br><strong>CamGovCA Team</strong></p>
            </div>
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
                <p>© 2025 CamGovCA - Cameroon Government Certificate Authority</p>
            </div>
        </body>
        </html>
        ";
        
        $text_content = "
        CamGovCA Two-Factor Authentication
        
        Hello {$user_name},
        
        Your two-factor authentication code is: {$otp}
        
        SECURITY WARNING:
        - This code will expire in {$expires_in}
        - NEVER share this code with anyone - CamGovCA staff will never ask for your OTP code
        - If you didn't request this code, DO NOT enter it and contact support immediately
        - This code is for your CamGovCA account login only
        
        Best regards,
        CamGovCA Team
        
        ---
        This is an automated message. Please do not reply to this email.
        © 2025 CamGovCA - Cameroon Government Certificate Authority
        ";
        
        return $this->sendEmail($to_email, $subject, $html_content, $text_content);
    }
    
    /**
     * Test email service
     */
    public function testConnection() {
        // Use a proper test email - you should replace this with your actual email for testing
        $test_email = 'test@resend.dev'; // Resend's testing domain
        
        $result = $this->sendEmail($test_email, 'Test Email from CamGovCA', '<p>This is a test email from CamGovCA 2FA system.</p>');
        
        if ($result['success']) {
            return ['success' => true, 'message' => 'Email service is working correctly'];
        } else {
            return ['success' => false, 'message' => 'Email service test failed: ' . $result['message']];
        }
    }
}

/**
 * Global email service instance
 */
$emailService = null;

/**
 * Get or create email service instance
 */
function getEmailService($service = null) {
    global $emailService;
    if (!$emailService || ($service && $emailService->service !== $service)) {
        $emailService = new EmailService($service);
    }
    return $emailService;
}
?> 