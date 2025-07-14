<?php
session_start();
require_once '../config/database.php';
require_once '../includes/2fa_verification.php';
require_once '../includes/audit_logger.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

$pdo = getDBConnection();
$verification = get2FAVerification($pdo);
$logger = getAuditLogger();

if (!$pdo) {
    die('Database connection failed');
}

$user_id = $_GET['id'] ?? 0;
$confirmed = $_GET['confirm'] ?? false;
$error = '';
$success = '';
$show2FAForm = false;

// Get user data
$stmt = $pdo->prepare("
    SELECT user_id, username, email, first_name, last_name, user_type, 
           COALESCE(status, 'active') as status
    FROM users 
    WHERE user_id = ? AND deleted_at IS NULL
");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: users.php');
    exit();
}

// Handle deletion with 2FA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        // Check if 2FA is required for user deletion
        if ($verification->is2FARequired('user_delete')) {
            // Get admin email from session or database
            $admin_email = $_SESSION['admin_email'] ?? null;
            if (!$admin_email) {
                // Fallback: get email from database
                $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
                $stmt->execute([$_SESSION['admin_user_id']]);
                $admin_user = $stmt->fetch();
                $admin_email = $admin_user['email'] ?? null;
            }
            
            // Generate and send 2FA code
            $result = $verification->generate2FACode('user_delete', $_SESSION['admin_user_id'], $admin_email);
            
            if ($result['success']) {
                $_SESSION['pending_user_deletion'] = $user_id;
                $_SESSION['generated_2fa_code'] = $result['code'] ?? null; // Store the generated code
                $show2FAForm = true;
                $success = 'Code de vérification envoyé à votre email.';
            } else {
                $error = 'Erreur lors de l\'envoi du code de vérification: ' . $result['message'];
            }
        } else {
            // No 2FA required, proceed with deletion
            performUserDeletion($user_id, $pdo, $logger);
        }
    } elseif (isset($_POST['verify_2fa_delete'])) {
        // Handle 2FA verification for deletion
        $code = trim($_POST['2fa_code'] ?? '');
        
        if (empty($code)) {
            $error = 'Please enter the verification code.';
        } else {
            $pending_user_id = $_SESSION['pending_user_deletion'] ?? null;
            
            if ($pending_user_id) {
                $result = $verification->verify2FACode($code, 'user_delete', $_SESSION['admin_user_id']);
                
                if ($result['success']) {
                    // Perform the deletion
                    performUserDeletion($pending_user_id, $pdo, $logger);
                    
                    // Clear pending data
                    unset($_SESSION['pending_user_deletion']);
                    unset($_SESSION['generated_2fa_code']); // Clear the generated code
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
 * Perform user deletion (soft delete)
 */
function performUserDeletion($user_id, $pdo, $logger) {
    try {
        // Don't allow deleting the current admin user
        if ($user_id == $_SESSION['admin_user_id']) {
            $error = 'You cannot delete your own account.';
            return;
        }
        
        $pdo->beginTransaction();
        
        // Soft delete the user
        $stmt = $pdo->prepare("
            UPDATE users 
            SET deleted_at = NOW(), 
                deleted_by = ?
            WHERE user_id = ?
        ");
        $stmt->execute([$_SESSION['admin_user_id'], $user_id]);
        
        if ($stmt->rowCount() > 0) {
            // Log the deletion
            $stmt = $pdo->prepare("
                INSERT INTO audit_logs (user_id, action, table_name, record_id, details, ip_address, created_at)
                VALUES (?, 'user_deleted', 'users', ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $_SESSION['admin_user_id'],
                $user_id,
                "User soft deleted by admin",
                $_SERVER['REMOTE_ADDR'] ?? ''
            ]);
            
            $pdo->commit();
            
            // Redirect with success message
            header('Location: users.php?deleted=1');
            exit();
        } else {
            $pdo->rollback();
            $error = 'User not found or already deleted.';
        }
        
    } catch (Exception $e) {
        $pdo->rollback();
        $error = 'Error deleting user: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA Admin - Delete User</title>
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
                <h2><i class="fas fa-user-times text-danger"></i> Supprimer un Utilisateur</h2>
                <a href="users.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour aux Utilisateurs
                </a>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$show2FAForm): ?>
                <!-- Confirmation Form -->
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5><i class="fas fa-exclamation-triangle"></i> Confirmer la Suppression</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i>
                            <strong>Attention:</strong> Cette action désactivera l'utilisateur mais conservera ses données pour des raisons d'audit.
                        </div>
                        
                        <div class="user-info bg-light p-3 rounded mb-3">
                            <h6><i class="fas fa-user"></i> Informations de l'utilisateur</h6>
                            <p><strong>Nom d'utilisateur:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                            <p><strong>Nom:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                            <p><strong>Type:</strong> <?php echo htmlspecialchars($user['user_type']); ?></p>
                        </div>
                        
                        <form method="POST">
                            <div class="d-flex gap-2">
                                <button type="submit" name="delete_user" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Confirmer la Suppression
                                </button>
                                <a href="users.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <!-- 2FA Verification Form -->
                <div class="2fa-form">
                    <h4><i class="fas fa-shield-alt"></i> Vérification en Deux Étapes</h4>
                    <p>Un code de vérification a été envoyé à votre email pour confirmer la suppression de l'utilisateur.</p>
                    
                    <?php if (isset($_SESSION['generated_2fa_code'])): ?>
                        <div class="alert alert-info">
                            <h6><i class="fas fa-key"></i> Code de Vérification (Développement)</h6>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary fs-5" id="code-display"><?php echo htmlspecialchars($_SESSION['generated_2fa_code']); ?></span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleCodeDisplay()">
                                    <i class="fas fa-eye"></i> Masquer
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="copyCode()">
                                    <i class="fas fa-copy"></i> Copier
                                </button>
                            </div>
                            <small class="text-muted">Ce code est affiché pour le développement. En production, il sera envoyé par email.</small>
                        </div>
                    <?php endif; ?>
                    
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
                            <button type="submit" name="verify_2fa_delete" class="btn btn-danger">
                                <i class="fas fa-check"></i> Confirmer la Suppression
                            </button>
                            <a href="users.php" class="btn btn-secondary">
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
        // Auto-focus on 2FA code input
        document.addEventListener('DOMContentLoaded', function() {
            const codeInput = document.getElementById('2fa_code');
            if (codeInput) {
                codeInput.focus();
            }
        });
        
        // Toggle code display (show/hide)
        function toggleCodeDisplay() {
            const codeDisplay = document.getElementById('code-display');
            const toggleBtn = event.target.closest('button');
            const icon = toggleBtn.querySelector('i');
            
            if (codeDisplay.style.filter === 'blur(5px)') {
                codeDisplay.style.filter = 'none';
                icon.className = 'fas fa-eye-slash';
                toggleBtn.innerHTML = '<i class="fas fa-eye-slash"></i> Masquer';
            } else {
                codeDisplay.style.filter = 'blur(5px)';
                icon.className = 'fas fa-eye';
                toggleBtn.innerHTML = '<i class="fas fa-eye"></i> Afficher';
            }
        }
        
        // Copy code to clipboard
        function copyCode() {
            const codeDisplay = document.getElementById('code-display');
            const code = codeDisplay.textContent;
            
            navigator.clipboard.writeText(code).then(function() {
                const copyBtn = event.target.closest('button');
                const originalText = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="fas fa-check"></i> Copié!';
                copyBtn.classList.remove('btn-outline-success');
                copyBtn.classList.add('btn-success');
                
                setTimeout(function() {
                    copyBtn.innerHTML = originalText;
                    copyBtn.classList.remove('btn-success');
                    copyBtn.classList.add('btn-outline-success');
                }, 2000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                alert('Impossible de copier le code. Veuillez le saisir manuellement.');
            });
        }
    </script>
</body>
</html> 