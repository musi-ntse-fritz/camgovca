<?php
session_start();
require_once 'config/database.php';

$pdo = getDBConnection();
$message = '';
$error = '';
$certificates = [];

// Get all active certificates
try {
    $stmt = $pdo->prepare("
        SELECT c.*, u.first_name, u.last_name, u.email,
               COALESCE(c.expires_at, DATE_ADD(c.created_at, INTERVAL 1 YEAR)) as expires_at
        FROM certificates c 
        LEFT JOIN users u ON c.user_id = u.user_id 
        WHERE c.status = 'active' 
        ORDER BY c.created_at DESC
    ");
    $stmt->execute();
    $certificates = $stmt->fetchAll();
} catch (Exception $e) {
    $error = 'Error loading certificates: ' . $e->getMessage();
}

// Handle revocation request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['revoke_certificate'])) {
        $certId = $_POST['cert_id'] ?? '';
        $reason = $_POST['revocation_reason'] ?? '';
        $admin_password = $_POST['admin_password'] ?? '';
        
        if (empty($certId) || empty($reason) || empty($admin_password)) {
            $error = 'Please provide certificate ID, revocation reason, and admin password.';
        } else {
            // Simple admin password validation (you can enhance this)
            if ($admin_password === 'admin123') { // Replace with proper admin validation
                try {
                    $pdo->beginTransaction();
                    
                    // Revoke the certificate
                    $stmt = $pdo->prepare("
                        UPDATE certificates 
                        SET status = 'revoked', 
                            revoked_at = NOW(), 
                            revocation_reason = ?
                        WHERE cert_id = ?
                    ");
                    $stmt->execute([$reason, $certId]);
                    
                    if ($stmt->rowCount() > 0) {
                        // Log the revocation
                        $stmt = $pdo->prepare("
                            INSERT INTO audit_logs (user_id, action, table_name, record_id, details, ip_address, created_at)
                            VALUES (?, 'certificate_revoked', 'certificates', ?, ?, ?, NOW())
                        ");
                        $stmt->execute([
                            1, // admin user id
                            $certId,
                            "Certificate revoked. Reason: $reason",
                            $_SERVER['REMOTE_ADDR'] ?? ''
                        ]);
                        
                        $pdo->commit();
                        $message = 'Certificate revoked successfully!';
                        
                        // Refresh the certificate list
                        $stmt = $pdo->prepare("
                            SELECT c.*, u.first_name, u.last_name, u.email,
                                   COALESCE(c.expires_at, DATE_ADD(c.created_at, INTERVAL 1 YEAR)) as expires_at
                            FROM certificates c 
                            LEFT JOIN users u ON c.user_id = u.user_id 
                            WHERE c.status = 'active' 
                            ORDER BY c.created_at DESC
                        ");
                        $stmt->execute();
                        $certificates = $stmt->fetchAll();
                    } else {
                        $pdo->rollback();
                        $error = 'Certificate not found or already revoked.';
                    }
                    
                } catch (Exception $e) {
                    $pdo->rollback();
                    $error = 'Error revoking certificate: ' . $e->getMessage();
                }
            } else {
                $error = 'Invalid admin password.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA - Révoquer un Certificat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .main-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 20px;
        }
        .certificate-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .certificate-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            border-radius: 8px;
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-active {
            background: #28a745;
            color: white;
        }
        .status-revoked {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-ban text-danger"></i> Révoquer un Certificat</h2>
                <a href="operations.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour aux Opérations
                </a>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <!-- Certificate List -->
            <div class="row">
                <?php if (empty($certificates)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Aucun certificat actif trouvé.
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($certificates as $cert): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="certificate-card">
                                <h5><i class="fas fa-certificate text-primary"></i> 
                                    <?php echo htmlspecialchars($cert['first_name'] . ' ' . $cert['last_name']); ?>
                                </h5>
                                <p><strong>Série:</strong> <?php echo htmlspecialchars($cert['serial_number']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($cert['email']); ?></p>
                                <p><strong>Statut:</strong> 
                                    <span class="status-badge status-active"><?php echo htmlspecialchars($cert['status']); ?></span>
                                </p>
                                <p><strong>Émis:</strong> <?php echo date('d/m/Y', strtotime($cert['created_at'])); ?></p>
                                <p><strong>Expire:</strong> <?php echo date('d/m/Y', strtotime($cert['expires_at'])); ?></p>
                                
                                <button type="button" class="btn btn-danger btn-sm w-100" 
                                        onclick="showRevokeForm('<?php echo $cert['cert_id']; ?>', '<?php echo htmlspecialchars($cert['first_name'] . ' ' . $cert['last_name']); ?>', '<?php echo htmlspecialchars($cert['serial_number']); ?>')">
                                    <i class="fas fa-ban"></i> Révoquer
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Revocation Form (Hidden by default) -->
            <div id="revokeForm" class="mt-4" style="display: none;">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5><i class="fas fa-exclamation-triangle"></i> Confirmer la Révocation</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" id="cert_id" name="cert_id">
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-certificate"></i> Certificat à révoquer
                                </label>
                                <input type="text" id="cert_name" class="form-control" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-hashtag"></i> Numéro de série
                                </label>
                                <input type="text" id="cert_serial" class="form-control" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label for="admin_password" class="form-label">
                                    <i class="fas fa-lock"></i> Mot de passe administrateur
                                </label>
                                <input type="password" class="form-control" id="admin_password" 
                                       name="admin_password" required placeholder="admin123">
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i> 
                                    Mot de passe admin: admin123
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="revocation_reason" class="form-label">
                                    <i class="fas fa-comment"></i> Raison de la révocation
                                </label>
                                <select class="form-control" id="revocation_reason" name="revocation_reason" required>
                                    <option value="">Sélectionnez une raison</option>
                                    <option value="Key compromise">Compromission de clé</option>
                                    <option value="CA compromise">Compromission de l'AC</option>
                                    <option value="Affiliation changed">Affiliation modifiée</option>
                                    <option value="Superseded">Remplacé</option>
                                    <option value="Cessation of operation">Cessation d'activité</option>
                                    <option value="Certificate hold">Suspension de certificat</option>
                                    <option value="Privilege withdrawn">Privilège retiré</option>
                                    <option value="Administrative">Administrative</option>
                                </select>
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Attention:</strong> Cette action est irréversible. Le certificat sera définitivement révoqué.
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" name="revoke_certificate" class="btn btn-danger">
                                    <i class="fas fa-ban"></i> Confirmer la Révocation
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="hideRevokeForm()">
                                    <i class="fas fa-times"></i> Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showRevokeForm(certId, certName, certSerial) {
            document.getElementById('cert_id').value = certId;
            document.getElementById('cert_name').value = certName;
            document.getElementById('cert_serial').value = certSerial;
            document.getElementById('revokeForm').style.display = 'block';
            document.getElementById('revokeForm').scrollIntoView({ behavior: 'smooth' });
        }
        
        function hideRevokeForm() {
            document.getElementById('revokeForm').style.display = 'none';
        }
    </script>
</body>
</html>