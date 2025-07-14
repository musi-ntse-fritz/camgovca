<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

$pdo = getDBConnection();
if (!$pdo) {
    die('Database connection failed');
}

// Handle certificate operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['cert_id'])) {
    $action = $_POST['action'];
    $cert_id = $_POST['cert_id'];
    
    try {
        switch ($action) {
            case 'revoke':
                $revocation_reason = $_POST['revocation_reason'] ?? 'Administrative revocation';
                
                // Update certificate status
                $stmt = $pdo->prepare("UPDATE certificates SET status = 'revoked', revoked_at = NOW() WHERE cert_id = ?");
                $stmt->execute([$cert_id]);
                
                // Log the revocation
                $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, table_name, record_id, new_values, ip_address) VALUES (?, 'REVOKE', 'certificates', ?, ?, ?)");
                $stmt->execute([
                    $_SESSION['admin_user_id'],
                    $cert_id,
                    json_encode(['revocation_reason' => $revocation_reason]),
                    $_SERVER['REMOTE_ADDR']
                ]);
                
                $success_message = "Certificate ID $cert_id has been revoked successfully.";
                break;
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Check if we just approved a certificate
$approval_message = '';
if (isset($_GET['approved']) && $_GET['approved'] == '1' && isset($_GET['cert_id'])) {
    $cert_id = $_GET['cert_id'];
    $approval_message = '<div style="background: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #c3e6cb;">
        ✅ Certificate approved successfully! Certificate ID: ' . htmlspecialchars($cert_id) . ' has been created and is now active.
    </div>';
}

// Get all certificates
$stmt = $pdo->query("SELECT c.cert_id, c.serial_number, c.subject_dn, c.cert_type, c.status, c.valid_from, c.valid_to, u.username FROM certificates c LEFT JOIN users u ON c.user_id = u.user_id ORDER BY c.created_at DESC");
$certificates = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamGovCA Admin - Certificate Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f6f9;
        }
        .header {
            background: #667eea;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .nav-links {
            display: flex;
            gap: 20px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .certificates-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .certificates-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .certificates-table th,
        .certificates-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .certificates-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        .status-revoked {
            color: #dc3545;
            font-weight: bold;
        }
        .status-expired {
            color: #ffc107;
            font-weight: bold;
        }
        .cert-type {
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
        }
        .btn-view {
            background: #17a2b8;
        }
        .btn-revoke {
            background: #dc3545;
        }
        .serial-number {
            font-family: monospace;
            font-size: 12px;
            color: #666;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Certificate Management</h1>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <?php if ($approval_message): ?>
            <?php echo $approval_message; ?>
        <?php endif; ?>
        
        <?php if (isset($success_message)): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #c3e6cb;">
                ✅ <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #f5c6cb;">
                ❌ <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <div class="page-header">
            <h2>All Certificates</h2>
            <a href="add-certificate.php" class="btn">Issue New Certificate</a>
        </div>
        
        <div class="certificates-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Serial Number</th>
                        <th>Subject</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Valid From</th>
                        <th>Valid To</th>
                        <th>Owner</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($certificates as $cert): ?>
                    <tr>
                        <td><?php echo $cert['cert_id']; ?></td>
                        <td class="serial-number"><?php echo htmlspecialchars($cert['serial_number']); ?></td>
                        <td><?php echo htmlspecialchars(substr($cert['subject_dn'], 0, 50)) . (strlen($cert['subject_dn']) > 50 ? '...' : ''); ?></td>
                        <td><span class="cert-type"><?php echo htmlspecialchars($cert['cert_type']); ?></span></td>
                        <td>
                            <span class="status-<?php echo $cert['status']; ?>">
                                <?php echo ucfirst(htmlspecialchars($cert['status'])); ?>
                            </span>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($cert['valid_from'])); ?></td>
                        <td><?php echo date('M j, Y', strtotime($cert['valid_to'])); ?></td>
                        <td><?php echo htmlspecialchars($cert['username'] ?? 'N/A'); ?></td>
                        <td>
                            <div class="actions">
                                <button onclick="viewCertificate(<?php echo $cert['cert_id']; ?>)" class="btn btn-small btn-view">View</button>
                                <?php if ($cert['status'] === 'active'): ?>
                                    <button onclick="revokeCertificate(<?php echo $cert['cert_id']; ?>)" class="btn btn-small btn-revoke">Revoke</button>
                                <?php else: ?>
                                    <span style="color: #999; font-size: 12px;">Already <?php echo $cert['status']; ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Revoke Certificate Modal -->
    <div id="revokeModal" class="modal" style="display: none;">
        <div class="modal-content" style="background: white; margin: 5% auto; padding: 20px; border-radius: 10px; width: 80%; max-width: 500px;">
            <span class="close" onclick="closeRevokeModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            <h3>Revoke Certificate</h3>
            <form method="POST">
                <input type="hidden" name="action" value="revoke">
                <input type="hidden" name="cert_id" id="revoke_cert_id">
                <div style="margin-bottom: 15px;">
                    <label for="revocation_reason" style="display: block; margin-bottom: 5px; font-weight: bold;">Reason for Revocation:</label>
                    <textarea name="revocation_reason" id="revocation_reason" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;" required>Administrative revocation</textarea>
                </div>
                <button type="submit" class="btn btn-revoke" style="background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Revoke Certificate</button>
                <button type="button" onclick="closeRevokeModal()" class="btn" style="background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Cancel</button>
            </form>
        </div>
    </div>

    <!-- View Certificate Modal -->
    <div id="viewModal" class="modal" style="display: none;">
        <div class="modal-content" style="background: white; margin: 5% auto; padding: 20px; border-radius: 10px; width: 90%; max-width: 800px; max-height: 80vh; overflow-y: auto;">
            <span class="close" onclick="closeViewModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            <h3>Certificate Details</h3>
            <div id="certificateDetails">
                <p>Loading certificate details...</p>
            </div>
        </div>
    </div>

    <script>
        function viewCertificate(certId) {
            // Show modal
            document.getElementById('viewModal').style.display = 'block';
            
            // Fetch certificate details via AJAX
            fetch('get_certificate_details.php?cert_id=' + certId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayCertificateDetails(data.certificate);
                    } else {
                        document.getElementById('certificateDetails').innerHTML = 
                            '<div style="color: red; text-align: center; padding: 20px;">Error: ' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    document.getElementById('certificateDetails').innerHTML = 
                        '<div style="color: red; text-align: center; padding: 20px;">Error loading certificate details</div>';
                });
        }

        function displayCertificateDetails(cert) {
            const details = `
                <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                    <h4>Certificate Information</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr><td style="padding: 8px; font-weight: bold;">Certificate ID:</td><td style="padding: 8px;">${cert.cert_id}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Serial Number:</td><td style="padding: 8px; font-family: monospace;">${cert.serial_number}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Subject DN:</td><td style="padding: 8px;">${cert.subject_dn}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Issuer DN:</td><td style="padding: 8px;">${cert.issuer_dn || 'N/A'}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Certificate Type:</td><td style="padding: 8px;">${cert.cert_type}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Status:</td><td style="padding: 8px;"><span style="color: ${cert.status === 'active' ? '#28a745' : '#dc3545'}; font-weight: bold;">${cert.status}</span></td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Valid From:</td><td style="padding: 8px;">${cert.valid_from}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Valid To:</td><td style="padding: 8px;">${cert.valid_to}</td></tr>
                        <tr><td style="padding: 8px; font-weight: bold;">Owner:</td><td style="padding: 8px;">${cert.username || 'N/A'}</td></tr>
                    </table>
                </div>
            `;
            document.getElementById('certificateDetails').innerHTML = details;
        }

        function revokeCertificate(certId) {
            if (confirm('Are you sure you want to revoke this certificate? This action cannot be undone.')) {
                document.getElementById('revoke_cert_id').value = certId;
                document.getElementById('revokeModal').style.display = 'block';
            }
        }

        function closeRevokeModal() {
            document.getElementById('revokeModal').style.display = 'none';
            document.getElementById('revocation_reason').value = 'Administrative revocation';
        }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const revokeModal = document.getElementById('revokeModal');
            const viewModal = document.getElementById('viewModal');
            if (event.target === revokeModal) {
                closeRevokeModal();
            }
            if (event.target === viewModal) {
                closeViewModal();
            }
        }
    </script>
</body>
</html> 