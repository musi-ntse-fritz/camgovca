<?php
require_once '../includes/functions.php';
$pdo = getDBConnection();
$stmt = $pdo->prepare("
    SELECT c.serial_number, c.valid_to, u.email, u.first_name, u.last_name
    FROM certificates c
    JOIN users u ON c.user_id = u.user_id
    WHERE c.status = 'active' 
    AND c.valid_to BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY)
");
$stmt->execute();
$expiring_certs = $stmt->fetchAll();
foreach ($expiring_certs as $cert) {
    $days_until_expiry = (strtotime($cert['valid_to']) - time()) / (60 * 60 * 24);
    echo "Certificate {$cert['serial_number']} expires in {$days_until_expiry} days (User: {$cert['email']})<br>\n";
}
?> 