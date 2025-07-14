<?php
session_start();
require_once 'config/database.php';
require_once 'includes/certificate_password_manager.php';

$pdo = getDBConnection();
$passwordManager = new CertificatePasswordManager($pdo);

$message = '';
$error = '';
$certificates = [];

// Get all active certificates
try {
    $stmt = $pdo->prepare("
        SELECT c.*, u.first_name, u.last_name, u.email, o.org_name 
        FROM certificates c 
        LEFT JOIN users u ON c.user_id = u.user_id 
        LEFT JOIN organizations o ON c.org_id = o.org_id 
        WHERE c.status = 'active' 
        ORDER BY c.created_at DESC
    ");
    $stmt->execute();
    $certificates = $stmt->fetchAll();
} catch (Exception $e) {
    $error = 'Error loading certificates: ' . $e->getMessage();
}

// Handle password change request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $certId = $_POST['cert_id'] ?? '';
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($certId) || empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'Please provide all required fields.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New password and confirmation password do not match.';
    } else {
        // Validate current certificate password
        $validation = $passwordManager->validateCertificatePassword($certId, $currentPassword);
        
        if ($validation['success']) {
            // Validate new password strength
            $passwordErrors = $passwordManager->validatePasswordStrength($newPassword);
            if (!empty($passwordErrors)) {
                $error = 'New password validation failed: ' . implode(', ', $passwordErrors);
            } else {
                try {
                    // Change the certificate password
                    $success = $passwordManager->setCertificatePassword($certId, $newPassword, $_SESSION['user_id'] ?? 1, 'change');
                    
                    if ($success) {
                        // Log the password change
                        $stmt = $pdo->prepare("
                            INSERT INTO audit_logs (user_id, action, details, ip_address, created_at)
                            VALUES (?, 'certificate_password_changed', ?, ?, NOW())
                        ");
                        $stmt->execute([
                            $_SESSION['user_id'] ?? 1,
                            "Certificate ID: $certId - Password changed successfully",
                            $_SERVER['REMOTE_ADDR'] ?? ''
                        ]);
                        
                        $message = 'Certificate password changed successfully!';
                        
                        // Clear form
                        $_POST = [];
                    } else {
                        $error = 'Failed to change certificate password.';
                    }
                    
                } catch (Exception $e) {
                    $error = 'Error changing certificate password: ' . $e->getMessage();
                }
            }
        } else {
            $error = $validation['message'];
        }
    }
}

$passwordRequirements = $passwordManager->getPasswordRequirements();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer le Mot de Passe du Certificat - CamGovCA</title>
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
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
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
            color: #17a2b8;
            border-bottom: 2px solid #17a2b8;
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
            border-color: #17a2b8;
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-button {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
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
            box-shadow: 0 5px 15px rgba(23,162,184,0.3);
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

        .password-requirements {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
        }

        .requirements-list {
            list-style: none;
            padding: 0;
            margin: 10px 0 0 0;
        }

        .requirements-list li {
            padding: 5px 0;
            font-size: 14px;
            color: #495057;
            position: relative;
            padding-left: 20px;
        }

        .requirements-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
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

        .password-strength {
            margin-top: 5px;
            font-size: 12px;
        }

        .strength-weak {
            color: #dc3545;
        }

        .strength-medium {
            color: #ffc107;
        }

        .strength-strong {
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dynamic_home.php" class="back-link">← Retour à l'accueil</a>
        
        <div class="header">
            <h1>Changer le Mot de Passe du Certificat</h1>
            <p>Modifier le mot de passe de sécurité d'un certificat électronique</p>
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
            <h3>🔐 Sécurité du Mot de Passe</h3>
            <p>Le changement de mot de passe du certificat :</p>
            <ul>
                <li>Améliore la sécurité de votre certificat</li>
                <li>Doit respecter les exigences de complexité</li>
                <li>Est recommandé en cas de compromission suspectée</li>
                <li>Nécessite le mot de passe actuel pour confirmation</li>
            </ul>
        </div>

        <div class="form-section">
            <h2 class="form-title">Formulaire de Changement de Mot de Passe</h2>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="cert_id" class="form-label">Sélectionner le Certificat *</label>
                    <select id="cert_id" name="cert_id" class="form-select" required>
                        <option value="">Choisir un certificat...</option>
                        <?php foreach ($certificates as $cert): ?>
                            <option value="<?php echo $cert['cert_id']; ?>">
                                <?php echo htmlspecialchars($cert['cert_id'] . ' - ' . $cert['subject_dn'] . ' (' . $cert['first_name'] . ' ' . $cert['last_name'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="current_password" class="form-label">Mot de Passe Actuel *</label>
                    <input type="password" id="current_password" name="current_password" class="form-input" required>
                    <small style="color: #666; font-size: 12px;">Entrez le mot de passe actuel du certificat</small>
                </div>

                <div class="form-group">
                    <label for="new_password" class="form-label">Nouveau Mot de Passe *</label>
                    <input type="password" id="new_password" name="new_password" class="form-input" required>
                    <div class="password-strength" id="password_strength"></div>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirmer le Nouveau Mot de Passe *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                    <small style="color: #666; font-size: 12px;">Répétez le nouveau mot de passe</small>
                </div>

                <div class="form-group password-requirements">
                    <label class="form-label">Exigences du Nouveau Mot de Passe:</label>
                    <ul class="requirements-list">
                        <?php foreach ($passwordRequirements as $requirement): ?>
                            <li><?php echo htmlspecialchars($requirement); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <button type="submit" name="change_password" class="form-button">
                    Changer le Mot de Passe
                </button>
            </form>
        </div>

        <div class="certificates-section">
            <h2 class="certificates-title">Certificats Disponibles</h2>
            
            <?php if (empty($certificates)): ?>
                <p>Aucun certificat actif trouvé.</p>
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
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($certificates as $cert): ?>
                            <tr>
                                <td class="cert-id"><?php echo htmlspecialchars($cert['cert_id']); ?></td>
                                <td><?php echo htmlspecialchars($cert['subject_dn']); ?></td>
                                <td><?php echo htmlspecialchars($cert['first_name'] . ' ' . $cert['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($cert['org_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($cert['cert_type']); ?></td>
                                <td><?php echo htmlspecialchars($cert['created_at']); ?></td>
                                <td><?php echo htmlspecialchars($cert['expiry_date']); ?></td>
                                <td class="status-active"><?php echo htmlspecialchars($cert['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            let feedback = [];
            
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            const strengthElement = document.getElementById('password_strength');
            
            if (strength < 3) {
                strengthElement.textContent = 'Faible';
                strengthElement.className = 'password-strength strength-weak';
            } else if (strength < 5) {
                strengthElement.textContent = 'Moyen';
                strengthElement.className = 'password-strength strength-medium';
            } else {
                strengthElement.textContent = 'Fort';
                strengthElement.className = 'password-strength strength-strong';
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const certId = document.getElementById('cert_id').value;
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (!certId || !currentPassword || !newPassword || !confirmPassword) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
                return false;
            }
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Le nouveau mot de passe et la confirmation ne correspondent pas.');
                return false;
            }
            
            if (newPassword.length < 8) {
                e.preventDefault();
                alert('Le nouveau mot de passe doit contenir au moins 8 caractères.');
                return false;
            }
        });

        // Password strength monitoring
        document.getElementById('new_password').addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });

        // Auto-fill certificate details when selected
        document.getElementById('cert_id').addEventListener('change', function() {
            const selectedCert = this.value;
            if (selectedCert) {
                console.log('Selected certificate for password change:', selectedCert);
            }
        });
    </script>
</body>
</html>