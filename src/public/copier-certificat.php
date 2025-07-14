<?php
session_start();
require_once 'config/database.php';

$page_title = "Copier un Certificat - ANTIC";
$page_description = "Copiez un certificat existant pour l'utiliser sur d'autres appareils ou systèmes";

$pdo = getDBConnection();
$copy_result = null;
$error_message = null;
$success_message = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $certificate_id = trim($_POST['certificate_id'] ?? '');
    $copy_format = $_POST['copy_format'] ?? '';
    $copy_password = $_POST['copy_password'] ?? '';
    $copy_purpose = $_POST['copy_purpose'] ?? '';
    
    if (empty($certificate_id) || empty($copy_format) || empty($copy_password)) {
        $error_message = 'Veuillez remplir tous les champs obligatoires.';
    } else {
        try {
            // Find the certificate
            $stmt = $pdo->prepare("SELECT c.*, u.first_name, u.last_name, u.email 
                                 FROM certificates c 
                                 LEFT JOIN users u ON c.user_id = u.user_id 
                                 WHERE c.serial_number = ? OR c.cert_id = ?");
            $stmt->execute([$certificate_id, $certificate_id]);
            $certificate = $stmt->fetch();
            
            if (!$certificate) {
                $error_message = 'Aucun certificat trouvé avec cet identifiant.';
            } elseif ($certificate['status'] !== 'active') {
                $error_message = 'Ce certificat n\'est pas actif et ne peut pas être copié.';
            } else {
                // Generate copy details
                $copy_id = 'COPY-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
                $copy_filename = $certificate['serial_number'] . '_copy.' . $copy_format;
                
                // Create copy record in database
                $stmt = $pdo->prepare("INSERT INTO certificate_copies (original_certificate_id, copy_id, copy_format, copy_purpose, created_at) 
                                     VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$certificate['cert_id'], $copy_id, $copy_format, $copy_purpose]);
                
                $copy_result = [
                    'copy_id' => $copy_id,
                    'original_certificate' => $certificate,
                    'copy_format' => $copy_format,
                    'copy_purpose' => $copy_purpose,
                    'filename' => $copy_filename,
                    'download_url' => 'download_certificate_copy.php?copy_id=' . $copy_id
                ];
                
                $success_message = 'Certificat copié avec succès. Vous pouvez maintenant télécharger la copie.';
            }
        } catch (Exception $e) {
            $error_message = 'Erreur lors de la copie: ' . $e->getMessage();
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
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; box-sizing: border-box; }
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
                <h1>Copier un Certificat</h1>
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
                <h2 class="page-title">Copier un Certificat Électronique</h2>
                <p class="page-description">Copiez un certificat existant pour l'utiliser sur d'autres appareils ou systèmes.</p>
            </div>

            <div class="highlight-box">
                <h4>À propos de la Copie de Certificat</h4>
                <p>La copie de certificat permet de dupliquer un certificat existant pour l'utiliser sur plusieurs appareils ou systèmes tout en conservant la même identité.</p>
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
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="certificate_id">Numéro de Série du Certificat Original *</label>
                        <input type="text" id="certificate_id" name="certificate_id" required placeholder="Ex: CERT-20241201-ABC12345" value="<?php echo htmlspecialchars($_POST['certificate_id'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="copy_format">Format de Copie *</label>
                        <select id="copy_format" name="copy_format" required>
                            <option value="">Sélectionnez un format</option>
                            <option value="p12" <?php echo ($_POST['copy_format'] ?? '') === 'p12' ? 'selected' : ''; ?>>PKCS#12 (.p12)</option>
                            <option value="pem" <?php echo ($_POST['copy_format'] ?? '') === 'pem' ? 'selected' : ''; ?>>PEM (.pem)</option>
                            <option value="der" <?php echo ($_POST['copy_format'] ?? '') === 'der' ? 'selected' : ''; ?>>DER (.der)</option>
                            <option value="pfx" <?php echo ($_POST['copy_format'] ?? '') === 'pfx' ? 'selected' : ''; ?>>PFX (.pfx)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="copy_password">Mot de Passe pour la Copie *</label>
                        <input type="password" id="copy_password" name="copy_password" required placeholder="Mot de passe pour protéger la copie">
                    </div>

                    <div class="form-group">
                        <label for="copy_purpose">Usage de la Copie</label>
                        <select id="copy_purpose" name="copy_purpose">
                            <option value="">Sélectionnez un usage</option>
                            <option value="backup" <?php echo ($_POST['copy_purpose'] ?? '') === 'backup' ? 'selected' : ''; ?>>Sauvegarde</option>
                            <option value="mobile" <?php echo ($_POST['copy_purpose'] ?? '') === 'mobile' ? 'selected' : ''; ?>>Appareil mobile</option>
                            <option value="server" <?php echo ($_POST['copy_purpose'] ?? '') === 'server' ? 'selected' : ''; ?>>Serveur</option>
                            <option value="development" <?php echo ($_POST['copy_purpose'] ?? '') === 'development' ? 'selected' : ''; ?>>Développement</option>
                            <option value="other" <?php echo ($_POST['copy_purpose'] ?? '') === 'other' ? 'selected' : ''; ?>>Autre</option>
                        </select>
                    </div>

                    <div style="text-align: center; margin-top: 30px;">
                        <button type="submit" class="btn">Copier le Certificat</button>
                        <a href="operations.php" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
                
                <?php if ($copy_result): ?>
                <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 10px; border-left: 4px solid #28a745;">
                    <h3 style="color: #28a745; margin-bottom: 15px;">✅ Copie de Certificat Créée</h3>
                    
                    <div style="background: white; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                        <h4>Détails de la Copie</h4>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 8px; font-weight: bold; width: 200px;">ID de Copie:</td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($copy_result['copy_id']); ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; font-weight: bold;">Certificat Original:</td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($copy_result['original_certificate']['serial_number']); ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; font-weight: bold;">Titulaire:</td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($copy_result['original_certificate']['first_name'] . ' ' . $copy_result['original_certificate']['last_name']); ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; font-weight: bold;">Format:</td>
                                <td style="padding: 8px;"><?php echo strtoupper(htmlspecialchars($copy_result['copy_format'])); ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; font-weight: bold;">Usage:</td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars(ucfirst($copy_result['copy_purpose'] ?: 'Non spécifié')); ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; font-weight: bold;">Fichier:</td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($copy_result['filename']); ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div style="text-align: center;">
                        <a href="<?php echo htmlspecialchars($copy_result['download_url']); ?>" class="btn" style="background: #28a745;">
                            📥 Télécharger la Copie du Certificat
                        </a>
                        <p style="margin-top: 10px; color: #666; font-size: 14px;">
                            ⚠️ Le fichier sera protégé par le mot de passe que vous avez fourni.
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>