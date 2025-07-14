<?php
require_once 'config/database.php';

$pdo = getDBConnection();
if (!$pdo) {
    die('Database connection failed');
}

echo "<h2>Final Certificate Functionality Test</h2>";

// Get available certificates for testing
$stmt = $pdo->query("SELECT cert_id, serial_number, status FROM certificates WHERE status = 'active' LIMIT 3");
$certificates = $stmt->fetchAll();

echo "<h3>Available Certificates for Testing:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Serial Number</th><th>Status</th></tr>";
foreach ($certificates as $cert) {
    echo "<tr>";
    echo "<td>{$cert['cert_id']}</td>";
    echo "<td>{$cert['serial_number']}</td>";
    echo "<td>{$cert['status']}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Testing Instructions:</h3>";
echo "<p><strong>For Certificate Verification:</strong></p>";
echo "<ol>";
echo "<li>Go to <a href='verifier-certificat.php' target='_blank'>verifier-certificat.php</a></li>";
echo "<li>Select 'Par numéro de série' as verification method</li>";
echo "<li>Enter one of the serial numbers above (e.g., {$certificates[0]['serial_number']})</li>";
echo "<li>Click 'Vérifier le Certificat'</li>";
echo "<li>You should see certificate details displayed</li>";
echo "</ol>";

echo "<p><strong>For Certificate Copy:</strong></p>";
echo "<ol>";
echo "<li>Go to <a href='copier-certificat.php' target='_blank'>copier-certificat.php</a></li>";
echo "<li>Enter one of the serial numbers above (e.g., {$certificates[0]['serial_number']})</li>";
echo "<li>Select a format (e.g., PEM)</li>";
echo "<li>Enter a password</li>";
echo "<li>Select a purpose (e.g., Backup)</li>";
echo "<li>Click 'Copier le Certificat'</li>";
echo "<li>You should see copy details and download link</li>";
echo "</ol>";

echo "<h3>✅ Expected Results:</h3>";
echo "<ul>";
echo "<li>✅ Certificate verification should show certificate details</li>";
echo "<li>✅ Certificate copy should create a copy record and show download link</li>";
echo "<li>✅ No more 'Please select a method' errors</li>";
echo "<li>✅ No more 'Column not found' errors</li>";
echo "</ul>";

echo "<h3>🔧 What Was Fixed:</h3>";
echo "<ul>";
echo "<li>✅ Fixed form validation logic in verification page</li>";
echo "<li>✅ Removed organizations table reference from queries</li>";
echo "<li>✅ Fixed column name mismatches (cert_id vs certificate_id)</li>";
echo "<li>✅ Added proper error handling and user feedback</li>";
echo "</ul>";

echo "<p><strong>🎉 Both certificate verification and copy functionality should now work correctly!</strong></p>";
?> 