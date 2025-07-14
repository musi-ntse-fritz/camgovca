<?php
session_start();

// Debug: Check if we're receiving POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<div style="background: #ff6b6b; color: white; padding: 20px; margin: 10px; border-radius: 5px; font-size: 16px;">';
    echo '<strong>POST DATA RECEIVED!</strong><br>';
    echo 'Action: ' . ($_POST['action'] ?? 'NOT SET') . '<br>';
    echo 'Request ID: ' . ($_POST['request_id'] ?? 'NOT SET') . '<br>';
    echo 'Session admin_logged_in: ' . ($_SESSION['admin_logged_in'] ?? 'NOT SET') . '<br>';
    echo 'Session admin_user_id: ' . ($_SESSION['admin_user_id'] ?? 'NOT SET') . '<br>';
    echo '</div>';
}

// Check if user is logged in and is admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login_step1.php');
    exit();
}

// Include database configuration and audit logger
require_once '../config/database.php';
require_once '../includes/audit_logger.php';

// Get certificate requests
$requests = [];
if (checkDatabaseConnection()) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT 
                cr.request_id,
                cr.request_type,
                cr.cert_type,
                cr.subject_dn,
                cr.status,
                cr.auth_code,
                cr.ref_code,
                cr.submitted_at,
                cr.processed_at,
                COALESCE(u.first_name, 'N/A') as first_name,
                COALESCE(u.last_name, 'N/A') as last_name,
                COALESCE(u.email, 'N/A') as email,
                COALESCE(u.phone, 'N/A') as phone,
                cr.organization as org_name
            FROM certificate_requests cr
            LEFT JOIN users u ON cr.user_id = u.user_id
            ORDER BY cr.submitted_at DESC
        ");
        $stmt->execute();
        $requests = $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error fetching certificate requests: " . $e->getMessage());
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['request_id'])) {
    $action = $_POST['action'];
    $request_id = $_POST['request_id'];
    
    try {
        $pdo = getDBConnection();
        $logger = getAuditLogger();
        
        switch ($action) {
            case 'approve':
                try {
                    $pdo->beginTransaction();
                    
                    // Update request status
                    $stmt = $pdo->prepare("UPDATE certificate_requests SET status = 'approved', processed_at = NOW(), processed_by = ? WHERE request_id = ?");
                    $stmt->execute([$_SESSION['admin_user_id'], $request_id]);
                    
                    // Log the approval action
                    $logger->logAdminOperation('certificate_approval', 'certificate_requests', $request_id, "Certificate request approved by admin");
                    
                    // Get request details for certificate creation
                    $stmt = $pdo->prepare("SELECT * FROM certificate_requests WHERE request_id = ?");
                    $stmt->execute([$request_id]);
                    $request = $stmt->fetch();
                    
                    if ($request) {
                        echo '<div style="background: #d4edda; color: #155724; padding: 15px; margin: 10px; border-radius: 5px;">Request details retrieved: ' . htmlspecialchars($request['subject_dn']) . '</div>';
                        echo '<div style="background: #d4edda; color: #155724; padding: 15px; margin: 10px; border-radius: 5px;">DEBUG: User ID from request: ' . htmlspecialchars($request['user_id']) . '</div>';
                        
                        // Generate certificate data
                        $serial_number = 'CERT-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
                        $valid_from = date('Y-m-d H:i:s');
                        $valid_to = date('Y-m-d H:i:s', strtotime('+1 year'));
                        $issue_date = date('Y-m-d H:i:s');
                        
                        echo '<div style="background: #d4edda; color: #155724; padding: 15px; margin: 10px; border-radius: 5px;">Generated serial number: ' . htmlspecialchars($serial_number) . '</div>';
                        
                        // Get the default CA (CamGovCA intermediate CA)
                        $ca_id = 2; // CamGovCA intermediate CA
                        $issuer_dn = "CN=CamGovCA,OU=Cameroon Government Certification Authority,O=ANTIC CA,C=CM";
                        
                        // Generate certificate data
                        $public_key = "-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----";
                        $certificate_pem = "-----BEGIN CERTIFICATE-----\nMIIFazCCA1OgAwIBAgIRAIIQz7DSQONZRGPgu2OCiwAwDQYJKoZIhvcNAQELBQAw\n...\n-----END CERTIFICATE-----";
                        $key_size = 2048;
                        $signature_algorithm = 'sha256WithRSAEncryption';
                        
                        // Insert all required fields
                        $stmt = $pdo->prepare("
                            INSERT INTO certificates 
                            (user_id, ca_id, serial_number, subject_dn, issuer_dn, public_key, key_size, signature_algorithm, certificate_pem, cert_type, status, valid_from, valid_to, auth_code, ref_code)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, ?, ?, ?)
                        ");
                        
                        $insertResult = $stmt->execute([
                            $request['user_id'],
                            $ca_id,
                            $serial_number,
                            $request['subject_dn'],
                            $issuer_dn,
                            $public_key,
                            $key_size,
                            $signature_algorithm,
                            $certificate_pem,
                            $request['cert_type'],
                            $valid_from,
                            $valid_to,
                            $request['auth_code'],
                            $request['ref_code']
                        ]);
                        
                        if (!$insertResult) {
                            throw new Exception("Failed to insert certificate: " . implode(", ", $stmt->errorInfo()));
                        }
                        
                        $certId = $pdo->lastInsertId();
                        echo '<div style="background: #d4edda; color: #155724; padding: 15px; margin: 10px; border-radius: 5px;">Certificate created successfully! Certificate ID: ' . $certId . '</div>';
                        
                        // Log the certificate issuance
                        $logger->logCertificateOperation('issue', $certId, null, [
                            'serial_number' => $serial_number,
                            'subject_dn' => $request['subject_dn'],
                            'cert_type' => $request['cert_type'],
                            'request_id' => $request_id
                        ], "Certificate issued from approved request");
                        
                    } else {
                        throw new Exception("Request not found after status update");
                    }
                    
                    $pdo->commit();
                    echo '<div style="background: #d4edda; color: #155724; padding: 15px; margin: 10px; border-radius: 5px;">Transaction committed successfully!</div>';
                    
                    // Redirect to certificates page after successful approval
                    header('Location: certificates.php?approved=1&cert_id=' . $certId);
                    exit();
                    
                } catch (Exception $e) {
                    $pdo->rollback();
                    echo '<div style="background: #f8d7da; color: #721c24; padding: 15px; margin: 10px; border-radius: 5px;">Error creating certificate: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    error_log("Error creating certificate: " . $e->getMessage());
                    
                    // Log the error
                    $logger->logSecurityEvent('certificate_creation_failed', [
                        'request_id' => $request_id,
                        'error' => $e->getMessage(),
                        'admin_user_id' => $_SESSION['admin_user_id'] ?? 'unknown'
                    ]);
                }
                break;
            
            case 'reject':
                $rejection_reason = $_POST['rejection_reason'] ?? '';
                $stmt = $pdo->prepare("UPDATE certificate_requests SET status = 'rejected', rejection_reason = ?, processed_at = NOW(), processed_by = ? WHERE request_id = ?");
                $stmt->execute([$rejection_reason, $_SESSION['admin_user_id'], $request_id]);
                
                // Log the rejection
                $logger->logAdminOperation('certificate_rejection', 'certificate_requests', $request_id, "Certificate request rejected: " . $rejection_reason);
                break;
                
            case 'complete':
                $stmt = $pdo->prepare("UPDATE certificate_requests SET status = 'completed', processed_at = NOW(), processed_by = ? WHERE request_id = ?");
                $stmt->execute([$_SESSION['admin_user_id'], $request_id]);
                
                // Log the completion
                $logger->logAdminOperation('certificate_completion', 'certificate_requests', $request_id, "Certificate request marked as completed");
                break;
        }
        
        // Handle redirects based on action type
        if ($action === 'approve') {
            // Approval redirect is handled within the approve case
            // No additional redirect needed here
        } else {
            // Redirect to refresh the page for other actions
            header('Location: certificate-requests.php');
            exit();
        }
    } catch (Exception $e) {
        error_log("Error updating certificate request: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demandes de Certificats - Admin ANTIC</title>
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 20px 0;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.8em;
            font-weight: 300;
        }

        .nav-links {
            display: flex;
            gap: 15px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            background: rgba(255,255,255,0.1);
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            background: rgba(255,255,255,0.2);
        }

        .main-content {
            padding: 30px 0;
        }

        .page-header {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .page-title {
            color: #1e3c72;
            font-size: 1.8em;
            margin-bottom: 10px;
        }

        .page-description {
            color: #666;
            font-size: 1.1em;
        }

        .requests-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .table-header {
            background: #1e3c72;
            color: white;
            padding: 20px;
        }

        .table-header h3 {
            font-size: 1.3em;
            font-weight: 300;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #1e3c72;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .status-completed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-approve {
            background: #28a745;
            color: white;
        }

        .btn-reject {
            background: #dc3545;
            color: white;
        }

        .btn-complete {
            background: #17a2b8;
            color: white;
        }

        .btn-view {
            background: #6c757d;
            color: white;
        }

        .btn:hover {
            opacity: 0.8;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="header-content">
                <h1>Administration CamGovCA</h1>
                <div class="nav-links">
                    <a href="dashboard.php">Tableau de Bord</a>
                    <a href="certificates.php">Certificats</a>
                    <a href="users.php">Utilisateurs</a>
                    <a href="audit-logs.php">Audit Logs</a>
                    <a href="logout.php">Déconnexion</a>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="page-header">
                <h2 class="page-title">Demandes de Certificats</h2>
                <p class="page-description">Gérez les demandes de certificats soumises par les utilisateurs</p>
            </div>

            <div class="requests-table">
                <div class="table-header">
                    <h3>Liste des Demandes</h3>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Demandeur</th>
                                <th>Type</th>
                                <th>Organisation</th>
                                <th>Statut</th>
                                <th>Date de Soumission</th>
                                <th>Code de Référence</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($requests)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 40px; color: #666;">
                                        Aucune demande de certificat trouvée
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($requests as $request): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($request['request_id']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?><br>
                                            <small><?php echo htmlspecialchars($request['email']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($request['cert_type']); ?></td>
                                        <td><?php echo htmlspecialchars($request['org_name'] ?: 'Individuel'); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $request['status']; ?>">
                                                <?php 
                                                $status_labels = [
                                                    'pending' => 'En Attente',
                                                    'approved' => 'Approuvé',
                                                    'rejected' => 'Rejeté',
                                                    'completed' => 'Terminé'
                                                ];
                                                echo $status_labels[$request['status']] ?? $request['status'];
                                                ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($request['submitted_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($request['ref_code']); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-view" onclick="viewRequest(<?php echo $request['request_id']; ?>)">Voir</button>
                                                <?php if ($request['status'] === 'pending'): ?>
                                                    <button class="btn btn-approve" onclick="approveRequest(<?php echo $request['request_id']; ?>)">Approuver</button>
                                                    <button class="btn btn-reject" onclick="rejectRequest(<?php echo $request['request_id']; ?>)">Rejeter</button>
                                                <?php elseif ($request['status'] === 'approved'): ?>
                                                    <button class="btn btn-complete" onclick="completeRequest(<?php echo $request['request_id']; ?>)">Terminer</button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Rejeter la Demande</h3>
            <form method="POST">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="request_id" id="reject_request_id">
                <div class="form-group">
                    <label for="rejection_reason">Raison du Rejet:</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-reject">Rejeter</button>
                <button type="button" class="btn" onclick="closeModal()">Annuler</button>
            </form>
        </div>
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeViewModal()">&times;</span>
            <h3>Détails de la Demande</h3>
            <div id="requestDetails">
                <p>Chargement...</p>
            </div>
        </div>
    </div>

    <script>
        function approveRequest(requestId) {
            if (confirm('Êtes-vous sûr de vouloir approuver cette demande de certificat ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="approve">
                    <input type="hidden" name="request_id" value="${requestId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function rejectRequest(requestId) {
            document.getElementById('reject_request_id').value = requestId;
            document.getElementById('rejectModal').style.display = 'block';
        }

        function completeRequest(requestId) {
            if (confirm('Êtes-vous sûr de vouloir marquer cette demande comme terminée ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="complete">
                    <input type="hidden" name="request_id" value="${requestId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function closeModal() {
            document.getElementById('rejectModal').style.display = 'none';
            document.getElementById('rejection_reason').value = '';
        }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
        }

        function viewRequest(requestId) {
            // Show modal
            document.getElementById('viewModal').style.display = 'block';
            
            // Fetch request details via AJAX
            fetch('get_request_details.php?request_id=' + requestId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayRequestDetails(data.request);
                    } else {
                        document.getElementById('requestDetails').innerHTML = 
                            '<div style="color: red; text-align: center; padding: 20px;">Erreur: ' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    document.getElementById('requestDetails').innerHTML = 
                        '<div style="color: red; text-align: center; padding: 20px;">Erreur de connexion</div>';
                });
        }

        function displayRequestDetails(request) {
            const certTypes = {
                'individual': 'Individuel',
                'organization': 'Organisation', 
                'ssl': 'SSL/TLS',
                'code_signing': 'Signature de Code',
                'email': 'Email'
            };

            const statusLabels = {
                'pending': 'En Attente',
                'approved': 'Approuvé',
                'rejected': 'Rejeté',
                'completed': 'Terminé'
            };

            const requestTypes = {
                'individual': 'Particulier',
                'company': 'Entreprise',
                'government': 'Institution Gouvernementale',
                'ngo': 'Organisation Non-Gouvernementale'
            };

            document.getElementById('requestDetails').innerHTML = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h4 style="color: #1e3c72; margin-bottom: 15px;">Type de Demande</h4>
                        <div style="margin-bottom: 10px;">
                            <strong>Type de Demandeur:</strong> ${requestTypes[request.request_type] || request.request_type}
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Type de Certificat:</strong> ${certTypes[request.cert_type] || request.cert_type}
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Nom du Sujet:</strong> ${request.subject_dn || 'Non spécifié'}
                        </div>
                    </div>
                    
                    <div>
                        <h4 style="color: #1e3c72; margin-bottom: 15px;">Informations de Contact</h4>
                        <div style="margin-bottom: 10px;">
                            <strong>Email:</strong> ${request.email || 'Non spécifié'}
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Téléphone:</strong> ${request.phone || 'Non spécifié'}
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Organisation:</strong> ${request.organization || 'Individuel'}
                        </div>
                    </div>
                    
                    <div>
                        <h4 style="color: #1e3c72; margin-bottom: 15px;">Statut et Codes</h4>
                        <div style="margin-bottom: 10px;">
                            <strong>Statut:</strong> <span class="status-badge status-${request.status}">${statusLabels[request.status] || request.status}</span>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Code de Référence:</strong> ${request.ref_code || 'Non généré'}
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Code d'Authentification:</strong> ${request.auth_code || 'Non généré'}
                        </div>
                    </div>
                    
                    <div>
                        <h4 style="color: #1e3c72; margin-bottom: 15px;">Dates</h4>
                        <div style="margin-bottom: 10px;">
                            <strong>Soumis le:</strong> ${request.submitted_at ? new Date(request.submitted_at).toLocaleString('fr-FR') : 'Non spécifié'}
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Traité le:</strong> ${request.processed_at ? new Date(request.processed_at).toLocaleString('fr-FR') : 'Non traité'}
                        </div>
                    </div>
                </div>
                
                ${request.rejection_reason ? `
                    <div style="margin-top: 20px; padding: 15px; background: #f8d7da; border-radius: 5px;">
                        <h4 style="color: #721c24; margin-bottom: 10px;">Raison du Rejet</h4>
                        <p>${request.rejection_reason}</p>
                    </div>
                ` : ''}
            `;
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const rejectModal = document.getElementById('rejectModal');
            const viewModal = document.getElementById('viewModal');
            if (event.target === rejectModal) {
                rejectModal.style.display = 'none';
            }
            if (event.target === viewModal) {
                viewModal.style.display = 'none';
            }
        }
    </script>
</body>
</html> 