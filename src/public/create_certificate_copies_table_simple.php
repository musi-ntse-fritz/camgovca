<?php
require_once 'config/database.php';

$pdo = getDBConnection();
if (!$pdo) {
    die('Database connection failed');
}

echo "<h2>Creating Certificate Copies Table (Simplified)</h2>";

try {
    // Drop table if it exists
    $pdo->exec("DROP TABLE IF EXISTS certificate_copies");
    
    // Create certificate_copies table without foreign key constraints
    $sql = "CREATE TABLE certificate_copies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        original_certificate_id INT NOT NULL,
        copy_id VARCHAR(50) UNIQUE NOT NULL,
        copy_format VARCHAR(10) NOT NULL,
        copy_purpose VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_by INT,
        status ENUM('active', 'revoked', 'expired') DEFAULT 'active',
        INDEX idx_copy_id (copy_id),
        INDEX idx_original_cert (original_certificate_id),
        INDEX idx_copy_status (status)
    )";
    
    $pdo->exec($sql);
    echo "<p>✅ certificate_copies table created successfully!</p>";
    
    // Check if table exists and show structure
    $stmt = $pdo->query("DESCRIBE certificate_copies");
    $columns = $stmt->fetchAll();
    
    echo "<h3>Table Structure:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Insert a sample copy record
    $stmt = $pdo->prepare("INSERT INTO certificate_copies (original_certificate_id, copy_id, copy_format, copy_purpose, created_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([1, 'COPY-20241201-TEST123', 'p12', 'backup', 1]);
    
    echo "<p>✅ Sample copy record inserted successfully!</p>";
    
    // Show sample data
    $stmt = $pdo->query("SELECT * FROM certificate_copies LIMIT 5");
    $copies = $stmt->fetchAll();
    
    echo "<h3>Sample Data:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Original Cert ID</th><th>Copy ID</th><th>Format</th><th>Purpose</th><th>Status</th><th>Created</th></tr>";
    foreach ($copies as $copy) {
        echo "<tr>";
        echo "<td>{$copy['id']}</td>";
        echo "<td>{$copy['original_certificate_id']}</td>";
        echo "<td>{$copy['copy_id']}</td>";
        echo "<td>{$copy['copy_format']}</td>";
        echo "<td>{$copy['copy_purpose']}</td>";
        echo "<td>{$copy['status']}</td>";
        echo "<td>{$copy['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><strong>✅ Certificate copies functionality is now ready!</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?> 