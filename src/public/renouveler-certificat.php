<?php
session_start();
require_once 'config/database.php';
require_once 'includes/certificate_password_manager.php';

$pdo = getDBConnection();
$passwordManager = new CertificatePasswordManager($pdo);

$message = '';
$error = '';
$certificates = [];

// Get certificates that can be renewed (active and near expiry)
try {
    $stmt = $pdo->prepare("
        SELECT c.*, u.first_name, u.last_name, u.email, o.org_name 
        FROM certificates c 
        LEFT JOIN users u ON c.user_id = u.user_id 
        LEFT JOIN organizations o ON c.org_id = o.org_id 
        WHERE c.status = 'active' 
        AND c.expiry_date > NOW()
        ORDER BY c.expiry_date ASC
    ");
    $stmt->execute();
    $certificates = $stmt->fetchAll();
} catch (Exception $e) {
    $error = 'Error loading certificates: ' . $e->getMessage();
}

// Handle renewal request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['renew_certificate'])) {
    $certId = $_POST['cert_id'] ?? '';
    $password = $_POST['certificate_password'] ?? '';
    $newValidity = $_POST['new_validity'] ?? '';
    
    if (empty($certId) || empty($password) || empty($newValidity)) {
        $error = 'Please provide certificate ID, password, and new validity period.';
    } else {
        // Validate certificate password
        $validation = $passwordManager->validateCertificatePassword($certId, $password);
        
        if ($validation['success']) {
            try {
                // Calculate new expiry date
                $newExpiryDate = date('Y-m-d H:i:s', strtotime("+$newValidity years"));
                
                // Create new certificate (renewal)
                $stmt = $pdo->prepare("
                    INSERT INTO certificates 
                    (user_id, org_id, cert_type, subject_dn, issuer_dn, serial_number, 
                     public_key, private_key, certificate_password, password_hash,
                     status, created_at, expiry_date, validity_period)
                    SELECT user_id, org_id, cert_type, subject_dn, issuer_dn, 
                           CONCAT('RENEW-', serial_number), public_key, private_key,
                           certificate_password, password_hash, 'active', NOW(), ?, ?
                    FROM certificates 
                    WHERE cert_id = ?
                ");
                $stmt->execute([$newExpiryDate, $newValidity, $certId]);
                
                $newCertId = $pdo->lastInsertId();
                
                // Revoke old certificate
                $stmt = $pdo->prepare("
                    UPDATE certificates 
                    SET status = 'renewed', 
                        revoked_at = NOW(), 
                        revocation_reason = 'renewed',
                        revoked_by = ?
                    WHERE cert_id = ?
                ");
                $stmt->execute([$_SESSION['user_id'] ?? 1, $certId]);
                
                // Log the renewal
                $stmt = $pdo->prepare("
                    INSERT INTO audit_logs (user_id, action, details, ip_address, created_at)
                    VALUES (?, 'certificate_renewed', ?, ?, NOW())
                ");
                $stmt->execute([
                    $_SESSION['user_id'] ?? 1,
                    "Old Certificate ID: $certId, New Certificate ID: $newCertId, Validity: $newValidity years",
                    $_SERVER['REMOTE_ADDR'] ?? ''
                ]);
                
                $message = "Certificate renewed successfully! New certificate ID: $newCertId";
                
            } catch (Exception $e) {
                $error = 'Error renewing certificate: ' . $e->getMessage();
            }
        } else {
            $error = $validation['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renouveler un Certificat - CamGovCA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
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

        .form-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .form-title {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: #28a745;
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #495057;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #28a745;
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-button {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40,167,69,0.3);
        }

        .certificates-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .certificates-title {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: #495057;
        }

        .certificate-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .certificate-table th,
        .certificate-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .certificate-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }

        .certificate-table tr:hover {
            background-color: #f8f9fa;
        }

        .cert-id {
            font-weight: bold;
            color: #007bff;
        }

        .status-active {
            color: #28a745;
            font-weight: bold;
        }

        .expiry-warning {
            color: #dc3545;
            font-weight: bold;
        }

        .expiry-soon {
            color: #ffc107;
            font-weight: bold;
        }

        .info-box {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-box h3 {
            margin-bottom: 10px;
            color: #0c5460;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dynamic_home.php" class="back-link">← Retour à l'accueil</a>
        
        <div class="header">
            <h1>Renouveler un Certificat</h1>
            <p>Prolonger la validité d'un certificat électronique</p>
        </div>

        <?php if ($message): ?>
            <div class="message success">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="info-box">
            <h3>ℹ️ Informations sur le Renouvellement</h3>
            <p>Le renouvellement de certificat permet de :</p>
            <ul>
                <li>Prolonger la validité du certificat avant expiration</li>
                <li>Conserver les mêmes informations d'identité</li>
                <li>Maintenir la continuité des services</li>
                <li>Éviter les interruptions de service</li>
            </ul>
        </div>

        <div class="form-section">
            <h2 class="form-title">Formulaire de Renouvellement</h2>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="cert_id" class="form-label">Sélectionner le Certificat *</label>
                    <select id="cert_id" name="cert_id" class="form-select" required>
                        <option value="">Choisir un certificat...</option>
                        <?php foreach ($certificates as $cert): ?>
                            <?php 
                            $expiryDate = new DateTime($cert['expiry_date']);
                            $now = new DateTime();
                            $daysUntilExpiry = $now->diff($expiryDate)->days;
                            $expiryClass = $daysUntilExpiry <= 30 ? 'expiry-warning' : ($daysUntilExpiry <= 90 ? 'expiry-soon' : '');
                            ?>
                            <option value="<?php echo $cert['cert_id']; ?>" class="<?php echo $expiryClass; ?>">
                                <?php echo htmlspecialchars($cert['cert_id'] . ' - ' . $cert['subject_dn'] . ' (' . $cert['first_name'] . ' ' . $cert['last_name'] . ') - Expire: ' . $cert['expiry_date']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="certificate_password" class="form-label">Mot de Passe du Certificat *</label>
                    <input type="password" id="certificate_password" name="certificate_password" class="form-input" required>
                    <small style="color: #666; font-size: 12px;">Entrez le mot de passe unique du certificat pour confirmer le renouvellement</small>
                </div>

                <div class="form-group">
                    <label for="new_validity" class="form-label">Nouvelle Période de Validité *</label>
                    <select id="new_validity" name="new_validity" class="form-select" required>
                        <option value="">Sélectionner la validité...</option>
                        <option value="1">1 an</option>
                        <option value="2">2 ans</option>
                        <option value="3">3 ans</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="renewal_reason" class="form-label">Raison du Renouvellement</label>
                    <textarea id="renewal_reason" name="renewal_reason" class="form-textarea" placeholder="Fournissez des détails sur la raison du renouvellement..."></textarea>
                </div>

                <button type="submit" name="renew_certificate" class="form-button">
                    Renouveler le Certificat
                </button>
            </form>
        </div>

        <div class="certificates-section">
            <h2 class="certificates-title">Certificats Éligibles au Renouvellement</h2>
            
            <?php if (empty($certificates)): ?>
                <p>Aucun certificat éligible au renouvellement trouvé.</p>
            <?php else: ?>
                <table class="certificate-table">
                    <thead>
                        <tr>
                            <th>ID Certificat</th>
                            <th>Sujet</th>
                            <th>Utilisateur</th>
                            <th>Organisation</th>
                            <th>Type</th>
                            <th>Date de Création</th>
                            <th>Date d'Expiration</th>
                            <th>Jours Restants</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($certificates as $cert): ?>
                            <?php 
                            $expiryDate = new DateTime($cert['expiry_date']);
                            $now = new DateTime();
                            $daysUntilExpiry = $now->diff($expiryDate)->days;
                            $expiryClass = $daysUntilExpiry <= 30 ? 'expiry-warning' : ($daysUntilExpiry <= 90 ? 'expiry-soon' : '');
                            ?>
                            <tr>
                                <td class="cert-id"><?php echo htmlspecialchars($cert['cert_id']); ?></td>
                                <td><?php echo htmlspecialchars($cert['subject_dn']); ?></td>
                                <td><?php echo htmlspecialchars($cert['first_name'] . ' ' . $cert['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($cert['org_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($cert['cert_type']); ?></td>
                                <td><?php echo htmlspecialchars($cert['created_at']); ?></td>
                                <td><?php echo htmlspecialchars($cert['expiry_date']); ?></td>
                                <td class="<?php echo $expiryClass; ?>"><?php echo $daysUntilExpiry; ?> jours</td>
                                <td class="status-active"><?php echo htmlspecialchars($cert['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const certId = document.getElementById('cert_id').value;
            const password = document.getElementById('certificate_password').value;
            const validity = document.getElementById('new_validity').value;
            
            if (!certId || !password || !validity) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
                return false;
            }
        });

        // Auto-fill certificate details when selected
        document.getElementById('cert_id').addEventListener('change', function() {
            const selectedCert = this.value;
            if (selectedCert) {
                // You can add AJAX here to fetch certificate details
                console.log('Selected certificate for renewal:', selectedCert);
            }
        });
    </script>
</body>
</html>