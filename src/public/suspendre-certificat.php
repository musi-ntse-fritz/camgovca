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
$pending_suspension = null;

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

// Handle suspension request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['suspend_certificate'])) {
        $certId = $_POST['cert_id'] ?? '';
        $password = $_POST['certificate_password'] ?? '';
        $reason = $_POST['suspension_reason'] ?? '';
        $duration = $_POST['suspension_duration'] ?? '';
        
        if (empty($certId) || empty($password) || empty($reason) || empty($duration)) {
            $error = 'Please provide certificate ID, password, suspension reason, and duration.';
        } else {
            // Validate certificate password
            $validation = $passwordManager->validateCertificatePassword($certId, $password);
            
            if ($validation['success']) {
                // Check if 2FA is required for certificate suspension
                if ($verification->is2FARequired('certificate_suspend')) {
                    // Store pending suspension data
                    $_SESSION['pending_suspension'] = [
                        'cert_id' => $certId,
                        'reason' => $reason,
                        'duration' => $duration,
                        'operation' => 'suspend'
                    ];
                    
                    // Generate and send 2FA code
                    $user_id = $_SESSION['user_id'] ?? 1;
                    $email = $_SESSION['user_email'] ?? 'admin@camgovca.cm';
                    
                    $result = $verification->generate2FACode('certificate_suspend', $user_id, $email);
                    
                    if ($result['success']) {
                        $show2FAForm = true;
                        $message = 'Code de vérification envoyé à votre email.';
                        $pending_suspension = $_SESSION['pending_suspension'];
                    } else {
                        $error = 'Erreur lors de l\'envoi du code de vérification: ' . $result['message'];
                    }
                } else {
                    // No 2FA required, proceed with suspension
                    performCertificateSuspension($certId, $reason, $duration, $pdo, $logger);
                }
            } else {
                $error = 'Invalid certificate password.';
            }
        }
    } elseif (isset($_POST['verify_2fa_suspend'])) {
        // Handle 2FA verification for suspension
        $code = trim($_POST['2fa_code'] ?? '');
        
        if (empty($code)) {
            $error = 'Please enter the verification code.';
        } else {
            $pending_suspension = $_SESSION['pending_suspension'] ?? null;
            
            if ($pending_suspension && $pending_suspension['operation'] === 'suspend') {
                $user_id = $_SESSION['user_id'] ?? 1;
                $result = $verification->verify2FACode($code, 'certificate_suspend', $user_id);
                
                if ($result['success']) {
                    // Perform the suspension
                    performCertificateSuspension(
                        $pending_suspension['cert_id'], 
                        $pending_suspension['reason'], 
                        $pending_suspension['duration'], 
                        $pdo, 
                        $logger
                    );
                    
                    // Clear pending data
                    unset($_SESSION['pending_suspension']);
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
 * Perform certificate suspension
 */
function performCertificateSuspension($certId, $reason, $duration, $pdo, $logger) {
    try {
        $pdo->beginTransaction();
        
        // Calculate suspension end date
        $suspensionEndDate = date('Y-m-d H:i:s', strtotime("+$duration days"));
        
        // Suspend the certificate
        $stmt = $pdo->prepare("
            UPDATE certificates 
            SET status = 'suspended', 
                suspended_at = NOW(), 
                suspension_reason = ?,
                suspension_end_date = ?,
                suspended_by = ?
            WHERE cert_id = ?
        ");
        $stmt->execute([$reason, $suspensionEndDate, $_SESSION['user_id'] ?? 1, $certId]);
        
        if ($stmt->rowCount() > 0) {
            // Log the suspension
            $stmt = $pdo->prepare("
                INSERT INTO audit_logs (user_id, action, table_name, record_id, details, ip_address, created_at)
                VALUES (?, 'certificate_suspended', 'certificates', ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $_SESSION['user_id'] ?? 1,
                $certId,
                "Certificate suspended. Reason: $reason, Duration: $duration days",
                $_SERVER['REMOTE_ADDR'] ?? ''
            ]);
            
            $pdo->commit();
            
            $message = 'Certificate suspended successfully!';
            $logger->logCertificateOperation('suspend', $certId, null, [
                'suspension_reason' => $reason,
                'duration_days' => $duration,
                'end_date' => $suspensionEndDate
            ]);
        } else {
            $pdo->rollback();
            $error = 'Certificate not found or already suspended.';
        }
        
    } catch (Exception $e) {
        $pdo->rollback();
        $error = 'Error suspending certificate: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA - Suspendre un Certificat</title>
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
        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            border: none;
            border-radius: 8px;
            color: #212529;
        }
        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
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
    </style>
</head>
<body>
    <div class="container">
        <div class="main-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-pause text-warning"></i> Suspendre un Certificat</h2>
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
                                <h5><i class="fas fa-certificate text-primary"></i> <?php echo htmlspecialchars($cert['subject_name'] ?? $cert['subject_dn']); ?></h5>
                                <p><strong>Série:</strong> <?php echo htmlspecialchars($cert['serial_number']); ?></p>
                                <p><strong>Type:</strong> <?php echo htmlspecialchars($cert['cert_type']); ?></p>
                                <p><strong>Statut:</strong> 
                                    <span class="badge bg-success"><?php echo htmlspecialchars($cert['status']); ?></span>
                                </p>
                                <p><strong>Expire:</strong> <?php echo date('d/m/Y', strtotime($cert['valid_to'])); ?></p>
                                
                                <button type="button" class="btn btn-warning btn-sm w-100" 
                                        onclick="showSuspendForm('<?php echo $cert['cert_id']; ?>', '<?php echo htmlspecialchars($cert['subject_name'] ?? $cert['subject_dn']); ?>')">
                                    <i class="fas fa-pause"></i> Suspendre
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Suspension Form (Hidden by default) -->
                <div id="suspendForm" class="mt-4" style="display: none;">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5><i class="fas fa-exclamation-triangle"></i> Confirmer la Suspension</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" id="cert_id" name="cert_id">
                                
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-certificate"></i> Certificat à suspendre
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
                                    <label for="suspension_reason" class="form-label">
                                        <i class="fas fa-comment"></i> Raison de la suspension
                                    </label>
                                    <select class="form-control" id="suspension_reason" name="suspension_reason" required>
                                        <option value="">Sélectionnez une raison</option>
                                        <option value="Security investigation">Enquête de sécurité</option>
                                        <option value="Suspicious activity">Activité suspecte</option>
                                        <option value="User request">Demande de l'utilisateur</option>
                                        <option value="Administrative hold">Suspension administrative</option>
                                        <option value="Compliance review">Révision de conformité</option>
                                        <option value="Technical issue">Problème technique</option>
                                        <option value="Legal requirement">Exigence légale</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="suspension_duration" class="form-label">
                                        <i class="fas fa-clock"></i> Durée de suspension (jours)
                                    </label>
                                    <select class="form-control" id="suspension_duration" name="suspension_duration" required>
                                        <option value="">Sélectionnez la durée</option>
                                        <option value="1">1 jour</option>
                                        <option value="3">3 jours</option>
                                        <option value="7">1 semaine</option>
                                        <option value="14">2 semaines</option>
                                        <option value="30">1 mois</option>
                                        <option value="90">3 mois</option>
                                        <option value="180">6 mois</option>
                                    </select>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Attention:</strong> Le certificat sera temporairement suspendu et ne pourra pas être utilisé pendant cette période.
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" name="suspend_certificate" class="btn btn-warning">
                                        <i class="fas fa-pause"></i> Confirmer la Suspension
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="hideSuspendForm()">
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
                    <p>Un code de vérification a été envoyé à votre email pour confirmer la suspension du certificat.</p>
                    
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
                            <button type="submit" name="verify_2fa_suspend" class="btn btn-warning">
                                <i class="fas fa-check"></i> Confirmer la Suspension
                            </button>
                            <a href="suspendre-certificat.php" class="btn btn-secondary">
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
        function showSuspendForm(certId, certName) {
            document.getElementById('cert_id').value = certId;
            document.getElementById('cert_name').value = certName;
            document.getElementById('suspendForm').style.display = 'block';
            document.getElementById('suspendForm').scrollIntoView({ behavior: 'smooth' });
        }
        
        function hideSuspendForm() {
            document.getElementById('suspendForm').style.display = 'none';
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