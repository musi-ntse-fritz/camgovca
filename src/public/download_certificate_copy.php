<?php
session_start();
require_once 'config/database.php';

$pdo = getDBConnection();

if (!isset($_GET['copy_id'])) {
    die('ID de copie manquant.');
}

$copy_id = $_GET['copy_id'];

try {
    // Get copy details
    $stmt = $pdo->prepare("SELECT cc.*, c.*, u.first_name, u.last_name, u.email 
                          FROM certificate_copies cc 
                          JOIN certificates c ON cc.original_certificate_id = c.cert_id 
                          LEFT JOIN users u ON c.user_id = u.user_id 
                          WHERE cc.copy_id = ?");
    $stmt->execute([$copy_id]);
    $copy_data = $stmt->fetch();
    
    if (!$copy_data) {
        die('Copie de certificat non trouvée.');
    }
    
    // Generate certificate content based on format
    $format = $copy_data['copy_format'];
    $filename = $copy_data['serial_number'] . '_copy.' . $format;
    
    // Set headers for download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    
    // Generate certificate content (simplified - in real implementation, you'd use OpenSSL)
    $certificate_content = generateCertificateContent($copy_data, $format);
    
    echo $certificate_content;
    
} catch (Exception $e) {
    die('Erreur lors du téléchargement: ' . $e->getMessage());
}

function generateCertificateContent($cert_data, $format) {
    // This is a simplified certificate generation
    // In a real implementation, you would use OpenSSL to generate actual certificates
    
    $subject = ($cert_data['first_name'] ?? 'Unknown') . ' ' . ($cert_data['last_name'] ?? 'User');
    $organization = 'CamGovCA'; // Default organization since we removed the organizations table
    $serial = $cert_data['serial_number'] ?? 'UNKNOWN';
    $email = $cert_data['email'] ?? 'user@camgovca.cm';
    
    switch ($format) {
        case 'pem':
            return "-----BEGIN CERTIFICATE-----\n" .
                   "MIIEpDCCA4ygAwIBAgIJAN" . substr(md5($serial), 0, 20) . "MA0GCSqGSIb3DQEBCwUA\n" .
                   "MIGLMQswCQYDVQQGEwJDTTELMAkGA1UECBMCTkEwMQYDVQQHEwpZQU9VTkRFQ0Ux\n" .
                   "GzAZBgNVBAoTElBBTlRJQy1DQU1FUk9PTi1DTzEMMAoGA1UECxMDQ0EwMQswCQYD\n" .
                   "VQQDEwJDTzEfMB0GCSqGSIb3DQEJARYQ" . base64_encode($email) . "MB4XDTI0MTIw\n" .
                   "MTAwMDAwMFoXDTI1MTIwMTAwMDAwMFowgYExCzAJBgNVBAYTAkNNMQswCQYDVQQI\n" .
                   "EwJOQTAxDjAMBgNVBAcTBVlBT1VORDEbMBkGA1UEChMSUEFOVElDLUNBTUVST04t\n" .
                   "Q08xDTALBgNVBAsTBFVTRVIxGTAXBgNVBAMTEC" . base64_encode($subject) . "x\n" .
                   "HzAdBgkqhkiG9w0BCQEWE" . base64_encode($email) . "wDQYJKoZIhvcNAQELBQAD\n" .
                   "ggEBABcQ" . substr(md5($serial), 0, 40) . "=\n" .
                   "-----END CERTIFICATE-----\n";
            
        case 'p12':
        case 'pfx':
            // For P12/PFX, we'd need to create a PKCS#12 container
            // This is a simplified version
            return "PKCS#12 Container for " . $subject . "\n" .
                   "Serial: " . $serial . "\n" .
                   "Organization: " . $organization . "\n" .
                   "Email: " . $email . "\n" .
                   "Generated: " . date('Y-m-d H:i:s') . "\n";
            
        case 'der':
            // DER is binary format, so we'd need to convert from PEM
            $pem_content = generateCertificateContent($cert_data, 'pem');
            // Remove PEM headers and decode base64
            $pem_clean = str_replace(['-----BEGIN CERTIFICATE-----', '-----END CERTIFICATE-----', "\n"], '', $pem_content);
            return base64_decode($pem_clean);
            
        default:
            return "Format non supporté: " . $format;
    }
}
?> 