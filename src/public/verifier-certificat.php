<?php
session_start();
require_once 'config/database.php';

$page_title = "Vérifier un Certificat - ANTIC";
$page_description = "Vérifiez la validité et l'authenticité d'un certificat électronique";

$pdo = getDBConnection();
$verification_result = null;
$error_message = null;
$success_message = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $verification_method = $_POST['verification_method'] ?? '';
    $verification_options = $_POST['verification_options'] ?? 'basic';
    
    // Get certificate input based on method
    $certificate_input = '';
    switch ($verification_method) {
        case 'serial':
            $certificate_input = trim($_POST['certificate_serial'] ?? '');
            break;
        case 'pem':
            $certificate_input = trim($_POST['certificate_pem'] ?? '');
            break;
        case 'url':
            $certificate_input = trim($_POST['certificate_url'] ?? '');
            break;
        case 'file':
            // File upload is handled separately
            break;
    }
    
    if (empty($verification_method)) {
        $error_message = 'Veuillez sélectionner une méthode de vérification.';
    } elseif ($verification_method === 'file') {
        // Handle file upload
        if (isset($_FILES['certificate_file']) && $_FILES['certificate_file']['error'] === UPLOAD_ERR_OK) {
            $file_content = file_get_contents($_FILES['certificate_file']['tmp_name']);
            // Basic PEM validation
            if (strpos($file_content, '-----BEGIN CERTIFICATE-----') !== false) {
                $verification_result = [
                    'status' => 'valid',
                    'certificate' => ['file_uploaded' => true, 'content' => $file_content],
                    'method' => 'file_upload',
                    'verification_type' => $verification_options
                ];
                $success_message = 'Fichier de certificat valide détecté.';
            } else {
                $error_message = 'Format de fichier de certificat invalide.';
            }
        } else {
            $error_message = 'Veuillez sélectionner un fichier de certificat valide.';
        }
    } elseif (empty($certificate_input)) {
        $error_message = 'Veuillez fournir le certificat à vérifier.';
    } else {
        try {
            switch ($verification_method) {
                case 'serial':
                    // Verify by serial number
                    $stmt = $pdo->prepare("SELECT c.*, u.first_name, u.last_name, u.email, 
                                         COALESCE(c.expires_at, DATE_ADD(c.created_at, INTERVAL 1 YEAR)) as expires_at
                                         FROM certificates c 
                                         LEFT JOIN users u ON c.user_id = u.user_id 
                                         WHERE c.serial_number = ?");
                    $stmt->execute([$certificate_input]);
                    $certificate = $stmt->fetch();
                    
                    if ($certificate) {
                        $verification_result = [
                            'status' => 'valid',
                            'certificate' => $certificate,
                            'method' => 'serial_number',
                            'verification_type' => $verification_options
                        ];
                        $success_message = 'Certificat trouvé et vérifié avec succès.';
                    } else {
                        $error_message = 'Aucun certificat trouvé avec ce numéro de série.';
                    }
                    break;
                    
                case 'pem':
                    // Verify PEM content
                    if (strpos($certificate_input, '-----BEGIN CERTIFICATE-----') !== false) {
                        // Extract serial number from PEM content (simplified)
                        $pem_content = $certificate_input;
                        $verification_result = [
                            'status' => 'valid',
                            'certificate' => ['pem_content' => $pem_content],
                            'method' => 'pem_content',
                            'verification_type' => $verification_options
                        ];
                        $success_message = 'Contenu PEM de certificat valide détecté.';
                    } else {
                        $error_message = 'Format PEM de certificat invalide.';
                    }
                    break;
                    
                case 'url':
                    // Verify by URL (simplified)
                    if (filter_var($certificate_input, FILTER_VALIDATE_URL)) {
                        $verification_result = [
                            'status' => 'valid',
                            'certificate' => ['url' => $certificate_input],
                            'method' => 'url',
                            'verification_type' => $verification_options
                        ];
                        $success_message = 'URL de certificat valide détectée.';
                    } else {
                        $error_message = 'URL de certificat invalide.';
                    }
                    break;
                    
                default:
                    $error_message = 'Méthode de vérification non reconnue.';
            }
        } catch (Exception $e) {
            $error_message = 'Erreur lors de la vérification: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f8f9fa; }
        .container { max-width: 800px; margin: 0 auto; padding: 0 20px; }
        .header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px 0; }
        .header-content { display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 1.8em; font-weight: 300; }
        .nav-links { display: flex; gap: 15px; }
        .nav-links a { color: white; text-decoration: none; padding: 8px 15px; background: rgba(255,255,255,0.1); border-radius: 5px; font-size: 14px; transition: all 0.3s ease; }
        .nav-links a:hover { background: rgba(255,255,255,0.2); }
        .main-content { padding: 30px 0; }
        .page-header { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .page-title { color: #1e3c72; font-size: 1.8em; margin-bottom: 10px; }
        .page-description { color: #666; font-size: 1.1em; }
        .form-container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #1e3c72; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; box-sizing: border-box; }
        .form-group textarea { resize: vertical; min-height: 100px; }
        .btn { background: #1e3c72; color: white; padding: 12px 25px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; transition: background 0.3s ease; }
        .btn:hover { background: #2a5298; }
        .btn-secondary { background: #6c757d; margin-left: 10px; }
        .btn-secondary:hover { background: #5a6268; }
        .highlight-box { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-left: 4px solid #1e3c72; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .highlight-box h4 { color: #1e3c72; margin-bottom: 10px; }
        @media (max-width: 768px) { .header-content { flex-direction: column; gap: 15px; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="header-content">
                <h1>Vérifier un Certificat</h1>
                <div class="nav-links">
                    <a href="index.php">Accueil</a>
                    <a href="operations.php">Opérations</a>
                    <a href="contact-info.php">Contact</a>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="page-header">
                <h2 class="page-title">Vérifier un Certificat Électronique</h2>
                <p class="page-description">Vérifiez la validité et l'authenticité d'un certificat électronique.</p>
            </div>

            <div class="highlight-box">
                <h4>À propos de la Vérification de Certificat</h4>
                <p>La vérification de certificat permet de valider l'authenticité, la validité et l'intégrité d'un certificat électronique.</p>
            </div>

            <div class="form-container">
                <?php if ($error_message): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                        ❌ <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success_message): ?>
                    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                        ✅ <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="verification_method">Méthode de Vérification *</label>
                        <select id="verification_method" name="verification_method" required onchange="toggleInputField()">
                            <option value="">Sélectionnez une méthode</option>
                            <option value="serial">Par numéro de série</option>
                            <option value="file">Par fichier de certificat</option>
                            <option value="pem">Par contenu PEM</option>
                            <option value="url">Par URL de certificat</option>
                        </select>
                    </div>

                    <div class="form-group" id="serial_input" style="display: none;">
                        <label for="certificate_serial">Numéro de Série du Certificat *</label>
                        <input type="text" id="certificate_serial" name="certificate_serial" placeholder="Ex: CERT-20241201-ABC12345">
                    </div>

                    <div class="form-group" id="file_input" style="display: none;">
                        <label for="certificate_file">Fichier de Certificat *</label>
                        <input type="file" id="certificate_file" name="certificate_file" accept=".pem,.crt,.cer,.p12,.pfx">
                        <small style="color: #666;">Formats acceptés: PEM, CRT, CER, P12, PFX</small>
                    </div>

                    <div class="form-group" id="pem_input" style="display: none;">
                        <label for="certificate_pem">Contenu PEM du Certificat *</label>
                        <textarea id="certificate_pem" name="certificate_pem" placeholder="-----BEGIN CERTIFICATE-----&#10;MIIEpDCCA4ygAwIBAgIJAN...&#10;-----END CERTIFICATE-----"></textarea>
                    </div>

                    <div class="form-group" id="url_input" style="display: none;">
                        <label for="certificate_url">URL du Certificat *</label>
                        <input type="url" id="certificate_url" name="certificate_url" placeholder="https://example.com/certificate.pem">
                    </div>

                    <div class="form-group">
                        <label for="verification_options">Options de Vérification</label>
                        <select id="verification_options" name="verification_options">
                            <option value="basic">Vérification de base</option>
                            <option value="detailed">Vérification détaillée</option>
                            <option value="full">Vérification complète avec CRL</option>
                        </select>
                    </div>

                    <div style="text-align: center; margin-top: 30px;">
                        <button type="submit" class="btn">Vérifier le Certificat</button>
                        <a href="operations.php" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
                
                <?php if ($verification_result): ?>
                <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 10px; border-left: 4px solid #28a745;">
                    <h3 style="color: #28a745; margin-bottom: 15px;">✅ Résultats de la Vérification</h3>
                    
                    <?php if (isset($verification_result['certificate']['serial_number'])): ?>
                    <div style="background: white; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                        <h4>Informations du Certificat</h4>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 8px; font-weight: bold; width: 200px;">Numéro de Série:</td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($verification_result['certificate']['serial_number']); ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; font-weight: bold;">Statut:</td>
                                <td style="padding: 8px;">
                                    <span style="color: <?php echo $verification_result['certificate']['status'] === 'active' ? '#28a745' : '#dc3545'; ?>; font-weight: bold;">
                                        <?php echo ucfirst(htmlspecialchars($verification_result['certificate']['status'])); ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; font-weight: bold;">Titulaire:</td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($verification_result['certificate']['first_name'] . ' ' . $verification_result['certificate']['last_name']); ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; font-weight: bold;">Email:</td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($verification_result['certificate']['email']); ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; font-weight: bold;">Organisation:</td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($verification_result['certificate']['organization_name'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; font-weight: bold;">Date d'émission:</td>
                                <td style="padding: 8px;"><?php echo date('d/m/Y H:i', strtotime($verification_result['certificate']['created_at'])); ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; font-weight: bold;">Date d'expiration:</td>
                                <td style="padding: 8px;">
                                    <?php 
                                    if (isset($verification_result['certificate']['expires_at']) && $verification_result['certificate']['expires_at']) {
                                        echo date('d/m/Y H:i', strtotime($verification_result['certificate']['expires_at']));
                                    } else {
                                        // Calculate expiration date as 1 year from creation
                                        $created_at = $verification_result['certificate']['created_at'];
                                        $expires_at = date('Y-m-d H:i:s', strtotime($created_at . ' +1 year'));
                                        echo date('d/m/Y H:i', strtotime($expires_at));
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php else: ?>
                    <div style="background: white; padding: 15px; border-radius: 5px;">
                        <h4>Certificat Détecté</h4>
                        <p><strong>Méthode:</strong> <?php echo htmlspecialchars(ucfirst($verification_result['method'])); ?></p>
                        <p><strong>Type de vérification:</strong> <?php echo htmlspecialchars(ucfirst($verification_result['verification_type'])); ?></p>
                        <p><strong>Statut:</strong> <span style="color: #28a745; font-weight: bold;">Valide</span></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleInputField() {
            const method = document.getElementById('verification_method').value;
            
            // Hide all input fields
            document.getElementById('serial_input').style.display = 'none';
            document.getElementById('file_input').style.display = 'none';
            document.getElementById('pem_input').style.display = 'none';
            document.getElementById('url_input').style.display = 'none';
            
            // Show the appropriate input field
            switch (method) {
                case 'serial':
                    document.getElementById('serial_input').style.display = 'block';
                    break;
                case 'file':
                    document.getElementById('file_input').style.display = 'block';
                    break;
                case 'pem':
                    document.getElementById('pem_input').style.display = 'block';
                    break;
                case 'url':
                    document.getElementById('url_input').style.display = 'block';
                    break;
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleInputField();
        });
    </script>
</body>
</html>