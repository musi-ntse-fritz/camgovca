<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

$pdo = getDBConnection();
if (!$pdo) {
    die('Database connection failed');
}

$message = '';
$error = '';

// Get users and organizations for dropdowns
$users_stmt = $pdo->query("SELECT user_id, username, first_name, last_name FROM users WHERE user_type = 'client' ORDER BY first_name, last_name");
$users = $users_stmt->fetchAll();

$orgs_stmt = $pdo->query("SELECT org_id, org_name FROM organizations ORDER BY org_name");
$organizations = $orgs_stmt->fetchAll();

if ($_POST) {
    $user_id = $_POST['user_id'] ?? '';
    $org_id = $_POST['org_id'] ?? '';
    $cert_type = $_POST['cert_type'] ?? 'ssl';
    $subject_name = $_POST['subject_name'] ?? '';
    $subject_alt_names = $_POST['subject_alt_names'] ?? '';
    $validity_period = $_POST['validity_period'] ?? 365;
    $key_size = $_POST['key_size'] ?? 2048;
    $signature_algorithm = $_POST['signature_algorithm'] ?? 'sha256';
    $status = $_POST['status'] ?? 'active';
    $notes = $_POST['notes'] ?? '';
    
    if (empty($user_id) || empty($cert_type) || empty($subject_name)) {
        $error = 'User, certificate type, and subject name are required.';
    } else {
        try {
            // Generate certificate serial number
            $serial_number = 'CERT-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
            
            // Calculate expiry date
            $issue_date = date('Y-m-d H:i:s');
            $expiry_date = date('Y-m-d H:i:s', strtotime("+{$validity_period} days"));
            
            // Get the default CA (assuming CA ID 1 exists)
            $ca_id = 1;
            
            // Generate certificate data
            $subject_dn = "CN={$subject_name},O=CamGovCA,C=CM";
            $issuer_dn = "CN=CamRootCA,OU=Cameroon Root Certification Authority,O=ANTIC,C=CM";
            $public_key = "-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----";
            $certificate_pem = "-----BEGIN CERTIFICATE-----\nMIIFazCCA1OgAwIBAgIRAIIQz7DSQONZRGPgu2OCiwAwDQYJKoZIhvcNAQELBQAw\n...\n-----END CERTIFICATE-----";
            
            // Generate certificate password
            require_once '../includes/certificate_password_manager.php';
            $passwordManager = new CertificatePasswordManager($pdo);
            $certificate_password = $passwordManager->generatePassword();
            $password_hash = $passwordManager->hashPassword($certificate_password);
            
            // Insert new certificate
            $stmt = $pdo->prepare("INSERT INTO certificates (user_id, org_id, ca_id, cert_type, subject_name, subject_dn, issuer_dn, subject_alt_names, serial_number, public_key, certificate_pem, issue_date, expiry_date, validity_period, key_size, signature_algorithm, status, notes, certificate_password, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $org_id, $ca_id, $cert_type, $subject_name, $subject_dn, $issuer_dn, $subject_alt_names, $serial_number, $public_key, $certificate_pem, $issue_date, $expiry_date, $validity_period, $key_size, $signature_algorithm, $status, $notes, $certificate_password, $password_hash]);
            
            $message = "Certificate created successfully!<br><strong>Generated Password: {$certificate_password}</strong><br>Please save this password securely - it will be required for all certificate operations.";
            
            // Clear form data
            $_POST = [];
        } catch (Exception $e) {
            $error = 'Error creating certificate: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA Admin - Add Certificate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f6f9;
        }
        .header {
            background: #667eea;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .nav-links {
            display: flex;
            gap: 20px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group textarea {
            height: 80px;
            resize: vertical;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .required {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Add New Certificate</h1>
        <div class="nav-links">
            <a href="certificates.php">Back to Certificates</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="form-card">
            <?php if ($message): ?>
                <div class="message success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="user_id">User <span class="required">*</span></label>
                        <select id="user_id" name="user_id" required>
                            <option value="">Select User</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['user_id']; ?>" <?php echo ($_POST['user_id'] ?? '') == $user['user_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['username'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="org_id">Organization</label>
                        <select id="org_id" name="org_id">
                            <option value="">Select Organization (Optional)</option>
                            <?php foreach ($organizations as $org): ?>
                                <option value="<?php echo $org['org_id']; ?>" <?php echo ($_POST['org_id'] ?? '') == $org['org_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($org['org_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="cert_type">Certificate Type <span class="required">*</span></label>
                        <select id="cert_type" name="cert_type" required>
                            <option value="ssl" <?php echo ($_POST['cert_type'] ?? '') == 'ssl' ? 'selected' : ''; ?>>SSL/TLS</option>
                            <option value="code_signing" <?php echo ($_POST['cert_type'] ?? '') == 'code_signing' ? 'selected' : ''; ?>>Code Signing</option>
                            <option value="email" <?php echo ($_POST['cert_type'] ?? '') == 'email' ? 'selected' : ''; ?>>Email</option>
                            <option value="client_auth" <?php echo ($_POST['cert_type'] ?? '') == 'client_auth' ? 'selected' : ''; ?>>Client Authentication</option>
                            <option value="document_signing" <?php echo ($_POST['cert_type'] ?? '') == 'document_signing' ? 'selected' : ''; ?>>Document Signing</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status <span class="required">*</span></label>
                        <select id="status" name="status" required>
                            <option value="active" <?php echo ($_POST['status'] ?? '') == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="pending" <?php echo ($_POST['status'] ?? '') == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="revoked" <?php echo ($_POST['status'] ?? '') == 'revoked' ? 'selected' : ''; ?>>Revoked</option>
                            <option value="expired" <?php echo ($_POST['status'] ?? '') == 'expired' ? 'selected' : ''; ?>>Expired</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="subject_name">Subject Name (Common Name) <span class="required">*</span></label>
                    <input type="text" id="subject_name" name="subject_name" value="<?php echo htmlspecialchars($_POST['subject_name'] ?? ''); ?>" placeholder="e.g., example.com" required>
                </div>
                
                <div class="form-group">
                    <label for="subject_alt_names">Subject Alternative Names</label>
                    <input type="text" id="subject_alt_names" name="subject_alt_names" value="<?php echo htmlspecialchars($_POST['subject_alt_names'] ?? ''); ?>" placeholder="e.g., www.example.com, mail.example.com">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="validity_period">Validity Period (Days) <span class="required">*</span></label>
                        <select id="validity_period" name="validity_period" required>
                            <option value="30" <?php echo ($_POST['validity_period'] ?? '') == '30' ? 'selected' : ''; ?>>30 Days</option>
                            <option value="90" <?php echo ($_POST['validity_period'] ?? '') == '90' ? 'selected' : ''; ?>>90 Days</option>
                            <option value="365" <?php echo ($_POST['validity_period'] ?? '') == '365' ? 'selected' : ''; ?>>1 Year</option>
                            <option value="730" <?php echo ($_POST['validity_period'] ?? '') == '730' ? 'selected' : ''; ?>>2 Years</option>
                            <option value="1095" <?php echo ($_POST['validity_period'] ?? '') == '1095' ? 'selected' : ''; ?>>3 Years</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="key_size">Key Size (Bits) <span class="required">*</span></label>
                        <select id="key_size" name="key_size" required>
                            <option value="2048" <?php echo ($_POST['key_size'] ?? '') == '2048' ? 'selected' : ''; ?>>2048</option>
                            <option value="4096" <?php echo ($_POST['key_size'] ?? '') == '4096' ? 'selected' : ''; ?>>4096</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="signature_algorithm">Signature Algorithm <span class="required">*</span></label>
                    <select id="signature_algorithm" name="signature_algorithm" required>
                        <option value="sha256" <?php echo ($_POST['signature_algorithm'] ?? '') == 'sha256' ? 'selected' : ''; ?>>SHA-256</option>
                        <option value="sha384" <?php echo ($_POST['signature_algorithm'] ?? '') == 'sha384' ? 'selected' : ''; ?>>SHA-384</option>
                        <option value="sha512" <?php echo ($_POST['signature_algorithm'] ?? '') == 'sha512' ? 'selected' : ''; ?>>SHA-512</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes"><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn">Create Certificate</button>
                    <a href="certificates.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 