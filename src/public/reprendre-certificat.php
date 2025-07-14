<?php
session_start();
require_once 'config/database.php';
require_once 'includes/certificate_password_manager.php';
require_once 'includes/2fa_verification.php';
require_once 'includes/audit_logger.php';

$pdo = getDBConnection();
$passwordManager = new CertificatePasswordManager($pdo);
$verification = get2FAVerification($pdo);
$logger = getAuditLogger();

$message = '';
$error = '';
$certificates = [];
$show2FAForm = false;
$pending_resume = null;

// Get all suspended certificates
try {
    // Check if suspended_at column exists
    $checkColumn = $pdo->query("SHOW COLUMNS FROM certificates LIKE 'suspended_at'");
    $columnExists = $checkColumn->rowCount() > 0;
    
    if ($columnExists) {
        $stmt = $pdo->prepare("
            SELECT c.*, u.first_name, u.last_name, u.email, o.org_name 
            FROM certificates c 
            LEFT JOIN users u ON c.user_id = u.user_id 
            LEFT JOIN organizations o ON c.org_id = o.org_id 
            WHERE c.status = 'suspended' 
            ORDER BY c.suspended_at DESC
        ");
    } else {
        // Fallback query without suspended_at column
        $stmt = $pdo->prepare("
            SELECT c.*, u.first_name, u.last_name, u.email, o.org_name 
            FROM certificates c 
            LEFT JOIN users u ON c.user_id = u.user_id 
            LEFT JOIN organizations o ON c.org_id = o.org_id 
            WHERE c.status = 'suspended' 
            ORDER BY c.updated_at DESC
        ");
    }
    $stmt->execute();
    $certificates = $stmt->fetchAll();
} catch (Exception $e) {
    $error = 'Error loading certificates: ' . $e->getMessage();
}

// Handle resume request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['resume_certificate'])) {
        $certId = $_POST['cert_id'] ?? '';
        $password = $_POST['certificate_password'] ?? '';
        $reason = $_POST['resume_reason'] ?? '';
        
        if (empty($certId) || empty($password) || empty($reason)) {
            $error = 'Please provide certificate ID, password, and resume reason.';
        } else {
            // Validate certificate password
            $validation = $passwordManager->validateCertificatePassword($certId, $password);
            
            if ($validation['success']) {
                // Check if 2FA is required for certificate resume
                if ($verification->is2FARequired('certificate_resume')) {
                    // Store pending resume data
                    $_SESSION['pending_resume'] = [
                        'cert_id' => $certId,
                        'reason' => $reason,
                        'operation' => 'resume'
                    ];
                    
                    // Generate and send 2FA code
                    $user_id = $_SESSION['user_id'] ?? 1;
                    $email = $_SESSION['user_email'] ?? 'admin@camgovca.cm';
                    
                    $result = $verification->generate2FACode('certificate_resume', $user_id, $email);
                    
                    if ($result['success']) {
                        $show2FAForm = true;
                        $message = 'Code de vérification envoyé à votre email.';
                        $pending_resume = $_SESSION['pending_resume'];
                    } else {
                        $error = 'Erreur lors de l\'envoi du code de vérification: ' . $result['message'];
                    }
                } else {
                    // No 2FA required, proceed with resume
                    performCertificateResume($certId, $reason, $pdo, $logger);
                }
            } else {
                $error = 'Invalid certificate password.';
            }
        }
    } elseif (isset($_POST['verify_2fa_resume'])) {
        // Handle 2FA verification for resume
        $code = trim($_POST['2fa_code'] ?? '');
        
        if (empty($code)) {
            $error = 'Please enter the verification code.';
        } else {
            $pending_resume = $_SESSION['pending_resume'] ?? null;
            
            if ($pending_resume && $pending_resume['operation'] === 'resume') {
                $user_id = $_SESSION['user_id'] ?? 1;
                $result = $verification->verify2FACode($code, 'certificate_resume', $user_id);
                
                if ($result['success']) {
                    // Perform the resume
                    performCertificateResume(
                        $pending_resume['cert_id'], 
                        $pending_resume['reason'], 
                        $pdo, 
                        $logger
                    );
                    
                    // Clear pending data
                    unset($_SESSION['pending_resume']);
                } else {
                    $error = $result['message'];
                }
            } else {
                $error = 'Invalid verification session.';
            }
        }
    }
}

/**
 * Perform certificate resume
 */
function performCertificateResume($certId, $reason, $pdo, $logger) {
    try {
        $pdo->beginTransaction();
        
        // Check if suspension columns exist
        $checkColumns = $pdo->query("SHOW COLUMNS FROM certificates LIKE 'suspended_at'");
        $columnsExist = $checkColumns->rowCount() > 0;
        
        if ($columnsExist) {
            // Resume the certificate with all suspension columns
            $stmt = $pdo->prepare("
                UPDATE certificates 
                SET status = 'active', 
                    resumed_at = NOW(), 
                    resume_reason = ?,
                    suspended_at = NULL,
                    suspension_reason = NULL,
                    suspension_end_date = NULL,
                    resumed_by = ?
                WHERE cert_id = ? AND status = 'suspended'
            ");
            $stmt->execute([$reason, $_SESSION['user_id'] ?? 1, $certId]);
        } else {
            // Resume the certificate without suspension columns
            $stmt = $pdo->prepare("
                UPDATE certificates 
                SET status = 'active'
                WHERE cert_id = ? AND status = 'suspended'
            ");
            $stmt->execute([$certId]);
        }
        
        if ($stmt->rowCount() > 0) {
            // Log the resume
            $stmt = $pdo->prepare("
                INSERT INTO audit_logs (user_id, action, table_name, record_id, details, ip_address, created_at)
                VALUES (?, 'certificate_resumed', 'certificates', ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $_SESSION['user_id'] ?? 1,
                $certId,
                "Certificate resumed. Reason: $reason",
                $_SERVER['REMOTE_ADDR'] ?? ''
            ]);
            
            $pdo->commit();
            
            $message = 'Certificate resumed successfully!';
            $logger->logCertificateOperation('resume', $certId, null, [
                'resume_reason' => $reason
            ]);
        } else {
            $pdo->rollback();
            $error = 'Certificate not found or not suspended.';
        }
        
    } catch (Exception $e) {
        $pdo->rollback();
        $error = 'Error resuming certificate: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA - Reprendre un Certificat</title>
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
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 8px;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
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
        .2fa-form {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        .suspension-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 10px;
            margin-top: 10px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-play text-success"></i> Reprendre un Certificat</h2>
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
            
            <?php if (!$show2FAForm): ?>
                <!-- Certificate List -->
                <div class="row">
                    <?php foreach ($certificates as $cert): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="certificate-card">
                                <h5><i class="fas fa-certificate text-warning"></i> <?php echo htmlspecialchars($cert['subject_name'] ?? $cert['subject_dn']); ?></h5>
                                <p><strong>Série:</strong> <?php echo htmlspecialchars($cert['serial_number']); ?></p>
                                <p><strong>Type:</strong> <?php echo htmlspecialchars($cert['cert_type']); ?></p>
                                <p><strong>Statut:</strong> 
                                    <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($cert['status']); ?></span>
                                </p>
                                <p><strong>Expire:</strong> <?php echo date('d/m/Y', strtotime($cert['valid_to'])); ?></p>
                                
                                <?php if (isset($cert['suspension_reason']) && $cert['suspension_reason']): ?>
                                    <div class="suspension-info">
                                        <strong>Raison de suspension:</strong> <?php echo htmlspecialchars($cert['suspension_reason']); ?><br>
                                        <?php if (isset($cert['suspended_at']) && $cert['suspended_at']): ?>
                                            <strong>Suspendu le:</strong> <?php echo date('d/m/Y H:i', strtotime($cert['suspended_at'])); ?>
                                        <?php endif; ?>
                                        <?php if (isset($cert['suspension_end_date']) && $cert['suspension_end_date']): ?>
                                            <br><strong>Fin prévue:</strong> <?php echo date('d/m/Y H:i', strtotime($cert['suspension_end_date'])); ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <button type="button" class="btn btn-success btn-sm w-100 mt-2" 
                                        onclick="showResumeForm('<?php echo $cert['cert_id']; ?>', '<?php echo htmlspecialchars($cert['subject_name'] ?? $cert['subject_dn']); ?>')">
                                    <i class="fas fa-play"></i> Reprendre
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (empty($certificates)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">Aucun certificat suspendu</h4>
                        <p class="text-muted">Tous les certificats sont actuellement actifs.</p>
                    </div>
                <?php endif; ?>
                
                <!-- Resume Form (Hidden by default) -->
                <div id="resumeForm" class="mt-4" style="display: none;">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5><i class="fas fa-check-circle"></i> Confirmer la Reprise</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" id="cert_id" name="cert_id">
                                
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-certificate"></i> Certificat à reprendre
                                    </label>
                                    <input type="text" id="cert_name" class="form-control" readonly>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="certificate_password" class="form-label">
                                        <i class="fas fa-lock"></i> Mot de passe du certificat
                                    </label>
                                    <input type="password" class="form-control" id="certificate_password" 
                                           name="certificate_password" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="resume_reason" class="form-label">
                                        <i class="fas fa-comment"></i> Raison de la reprise
                                    </label>
                                    <select class="form-control" id="resume_reason" name="resume_reason" required>
                                        <option value="">Sélectionnez une raison</option>
                                        <option value="Investigation completed">Enquête terminée</option>
                                        <option value="Security concern resolved">Préoccupation de sécurité résolue</option>
                                        <option value="User request">Demande de l'utilisateur</option>
                                        <option value="Administrative decision">Décision administrative</option>
                                        <option value="Compliance verified">Conformité vérifiée</option>
                                        <option value="Technical issue resolved">Problème technique résolu</option>
                                        <option value="Legal requirement met">Exigence légale satisfaite</option>
                                    </select>
                                </div>
                                
                                <div class="alert alert-success">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Information:</strong> Le certificat sera réactivé et redeviendra utilisable immédiatement.
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" name="resume_certificate" class="btn btn-success">
                                        <i class="fas fa-play"></i> Confirmer la Reprise
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="hideResumeForm()">
                                        <i class="fas fa-times"></i> Annuler
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- 2FA Verification Form -->
                <div class="2fa-form">
                    <h4><i class="fas fa-shield-alt"></i> Vérification en Deux Étapes</h4>
                    <p>Un code de vérification a été envoyé à votre email pour confirmer la reprise du certificat.</p>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="2fa_code" class="form-label">
                                <i class="fas fa-key"></i> Code de vérification
                            </label>
                            <input type="text" class="form-control" id="2fa_code" name="2fa_code" 
                                   placeholder="Entrez le code à 6 chiffres" maxlength="6" required>
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i> 
                                Le code expire dans 10 minutes.
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" name="verify_2fa_resume" class="btn btn-success">
                                <i class="fas fa-check"></i> Confirmer la Reprise
                            </button>
                            <a href="reprendre-certificat.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showResumeForm(certId, certName) {
            document.getElementById('cert_id').value = certId;
            document.getElementById('cert_name').value = certName;
            document.getElementById('resumeForm').style.display = 'block';
            document.getElementById('resumeForm').scrollIntoView({ behavior: 'smooth' });
        }
        
        function hideResumeForm() {
            document.getElementById('resumeForm').style.display = 'none';
        }
        
        // Auto-focus on 2FA code input
        document.addEventListener('DOMContentLoaded', function() {
            const codeInput = document.getElementById('2fa_code');
            if (codeInput) {
                codeInput.focus();
            }
        });
    </script>
</body>
</html> 