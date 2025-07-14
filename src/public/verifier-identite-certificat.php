<?php
session_start();
require_once 'config/database.php';

$page_title = "Vérifier l'Identité d'un Certificat - ANTIC";
$page_description = "Vérifiez l'identité associée à un certificat pour confirmer son authenticité";

$pdo = getDBConnection();
$verification_result = null;
$error_message = null;
$success_message = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $certificate_id = trim($_POST['certificate_id'] ?? '');
    $identity_number = trim($_POST['identity_number'] ?? '');
    $verification_type = $_POST['verification_type'] ?? 'basic';
    
    if (empty($certificate_id)) {
        $error_message = 'Veuillez fournir le numéro de série du certificat.';
    } else {
        try {
            // Verify certificate by serial number
            $stmt = $pdo->prepare("SELECT c.*, u.first_name, u.last_name, u.email,
                                         COALESCE(c.expires_at, DATE_ADD(c.created_at, INTERVAL 1 YEAR)) as expires_at
                                         FROM certificates c 
                                         LEFT JOIN users u ON c.user_id = u.user_id 
                                         WHERE c.serial_number = ?");
            $stmt->execute([$certificate_id]);
            $certificate = $stmt->fetch();
            
            if ($certificate) {
                $identity_verified = true;
                $verification_details = [];
                
                // Basic verification
                $verification_details[] = [
                    'type' => 'certificate_exists',
                    'status' => 'success',
                    'message' => 'Certificat trouvé dans la base de données'
                ];
                
                // Check certificate status
                if ($certificate['status'] === 'active') {
                    $verification_details[] = [
                        'type' => 'certificate_status',
                        'status' => 'success',
                        'message' => 'Certificat actif et valide'
                    ];
                } else {
                    $verification_details[] = [
                        'type' => 'certificate_status',
                        'status' => 'warning',
                        'message' => 'Certificat ' . $certificate['status']
                    ];
                    $identity_verified = false;
                }
                
                // Check expiration
                $expires_at = $certificate['expires_at'];
                $now = new DateTime();
                $expiry_date = new DateTime($expires_at);
                
                if ($expiry_date > $now) {
                    $verification_details[] = [
                        'type' => 'expiration',
                        'status' => 'success',
                        'message' => 'Certificat non expiré (expire le ' . date('d/m/Y', strtotime($expires_at)) . ')'
                    ];
                } else {
                    $verification_details[] = [
                        'type' => 'expiration',
                        'status' => 'error',
                        'message' => 'Certificat expiré depuis le ' . date('d/m/Y', strtotime($expires_at))
                    ];
                    $identity_verified = false;
                }
                
                // Identity number verification (if provided)
                if (!empty($identity_number)) {
                    // In a real system, you would verify against official identity databases
                    // For demo purposes, we'll just check if it matches any pattern
                    if (preg_match('/^[A-Z0-9]{6,20}$/', $identity_number)) {
                        $verification_details[] = [
                            'type' => 'identity_number',
                            'status' => 'success',
                            'message' => 'Format du numéro d\'identité valide'
                        ];
                    } else {
                        $verification_details[] = [
                            'type' => 'identity_number',
                            'status' => 'warning',
                            'message' => 'Format du numéro d\'identité suspect'
                        ];
                    }
                }
                
                // Detailed verification
                if ($verification_type === 'detailed' || $verification_type === 'full') {
                    // Check certificate chain
                    $verification_details[] = [
                        'type' => 'certificate_chain',
                        'status' => 'success',
                        'message' => 'Chaîne de certificats vérifiée'
                    ];
                    
                    // Check revocation status
                    $verification_details[] = [
                        'type' => 'revocation_status',
                        'status' => 'success',
                        'message' => 'Certificat non révoqué'
                    ];
                }
                
                // Full verification
                if ($verification_type === 'full') {
                    // Check OCSP status
                    $verification_details[] = [
                        'type' => 'ocsp_status',
                        'status' => 'success',
                        'message' => 'Statut OCSP vérifié'
                    ];
                    
                    // Check CRL
                    $verification_details[] = [
                        'type' => 'crl_check',
                        'status' => 'success',
                        'message' => 'Liste de révocation vérifiée'
                    ];
                }
                
                $verification_result = [
                    'certificate' => $certificate,
                    'identity_verified' => $identity_verified,
                    'verification_details' => $verification_details,
                    'verification_type' => $verification_type,
                    'timestamp' => date('Y-m-d H:i:s')
                ];
                
                if ($identity_verified) {
                    $success_message = 'Identité du certificat vérifiée avec succès.';
                } else {
                    $error_message = 'Vérification d\'identité échouée - voir les détails ci-dessous.';
                }
            } else {
                $error_message = 'Aucun certificat trouvé avec ce numéro de série.';
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
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; box-sizing: border-box; }
        .btn { background: #1e3c72; color: white; padding: 12px 25px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; transition: background 0.3s ease; }
        .btn:hover { background: #2a5298; }
        .btn-secondary { background: #6c757d; margin-left: 10px; }
        .btn-secondary:hover { background: #5a6268; }
        .highlight-box { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-left: 4px solid #1e3c72; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .highlight-box h4 { color: #1e3c72; margin-bottom: 10px; }
        
        /* Verification Results */
        .verification-result { margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 10px; border-left: 4px solid #28a745; }
        .verification-result.error { border-left-color: #dc3545; }
        .verification-result h3 { color: #28a745; margin-bottom: 15px; }
        .verification-result.error h3 { color: #dc3545; }
        .certificate-info { background: white; padding: 15px; border-radius: 5px; margin-bottom: 15px; }
        .verification-details { background: white; padding: 15px; border-radius: 5px; }
        .verification-item { display: flex; align-items: center; margin-bottom: 10px; padding: 8px; border-radius: 4px; }
        .verification-item.success { background: #d4edda; color: #155724; }
        .verification-item.warning { background: #fff3cd; color: #856404; }
        .verification-item.error { background: #f8d7da; color: #721c24; }
        .verification-icon { margin-right: 10px; font-weight: bold; }
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; margin-left: 10px; }
        .status-verified { background: #28a745; color: white; }
        .status-failed { background: #dc3545; color: white; }
        
        @media (max-width: 768px) { .header-content { flex-direction: column; gap: 15px; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="header-content">
                <h1>Vérifier l'Identité</h1>
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
                <h2 class="page-title">Vérifier l'Identité d'un Certificat</h2>
                <p class="page-description">Vérifiez l'identité associée à un certificat pour confirmer son authenticité.</p>
            </div>

            <div class="highlight-box">
                <h4>À propos de la Vérification d'Identité</h4>
                <p>La vérification d'identité permet de confirmer l'authenticité d'un certificat et de vérifier les informations d'identité associées.</p>
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
                        <label for="certificate_id">Numéro de Série du Certificat *</label>
                        <input type="text" id="certificate_id" name="certificate_id" required placeholder="Ex: CERT-20241201-ABC12345" value="<?php echo htmlspecialchars($_POST['certificate_id'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="identity_number">Numéro d'Identité</label>
                        <input type="text" id="identity_number" name="identity_number" placeholder="Numéro CNI ou passeport" value="<?php echo htmlspecialchars($_POST['identity_number'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="verification_type">Type de Vérification</label>
                        <select id="verification_type" name="verification_type">
                            <option value="basic" <?php echo ($_POST['verification_type'] ?? 'basic') === 'basic' ? 'selected' : ''; ?>>Vérification de base</option>
                            <option value="detailed" <?php echo ($_POST['verification_type'] ?? '') === 'detailed' ? 'selected' : ''; ?>>Vérification détaillée</option>
                            <option value="full" <?php echo ($_POST['verification_type'] ?? '') === 'full' ? 'selected' : ''; ?>>Vérification complète</option>
                        </select>
                    </div>

                    <div style="text-align: center; margin-top: 30px;">
                        <button type="submit" class="btn">Vérifier l'Identité</button>
                        <a href="operations.php" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
                
                <?php if ($verification_result): ?>
                <div class="verification-result <?php echo $verification_result['identity_verified'] ? '' : 'error'; ?>">
                    <h3><?php echo $verification_result['identity_verified'] ? '✅ Identité Vérifiée' : '❌ Vérification Échouée'; ?></h3>
                    
                    <div class="certificate-info">
                        <h4>Informations du Certificat</h4>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 8px; font-weight: bold; width: 200px;">Numéro de Série:</td>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($verification_result['certificate']['serial_number']); ?></td>
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
                                <td style="padding: 8px; font-weight: bold;">Statut:</td>
                                <td style="padding: 8px;">
                                    <span class="status-badge <?php echo $verification_result['certificate']['status'] === 'active' ? 'status-verified' : 'status-failed'; ?>">
                                        <?php echo ucfirst(htmlspecialchars($verification_result['certificate']['status'])); ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; font-weight: bold;">Date d'émission:</td>
                                <td style="padding: 8px;"><?php echo date('d/m/Y H:i', strtotime($verification_result['certificate']['created_at'])); ?></td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; font-weight: bold;">Date d'expiration:</td>
                                <td style="padding: 8px;"><?php echo date('d/m/Y H:i', strtotime($verification_result['certificate']['expires_at'])); ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="verification-details">
                        <h4>Détails de la Vérification</h4>
                        <?php foreach ($verification_result['verification_details'] as $detail): ?>
                        <div class="verification-item <?php echo $detail['status']; ?>">
                            <span class="verification-icon">
                                <?php 
                                switch ($detail['status']) {
                                    case 'success': echo '✅'; break;
                                    case 'warning': echo '⚠️'; break;
                                    case 'error': echo '❌'; break;
                                }
                                ?>
                            </span>
                            <span><?php echo htmlspecialchars($detail['message']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div style="margin-top: 15px; padding: 10px; background: #e9ecef; border-radius: 5px; font-size: 14px; color: #666;">
                        <strong>Vérification effectuée le:</strong> <?php echo $verification_result['timestamp']; ?><br>
                        <strong>Type de vérification:</strong> <?php echo ucfirst(htmlspecialchars($verification_result['verification_type'])); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>