<?php
require_once 'config/database.php';

$pdo = getDBConnection();
if (!$pdo) {
    die('Database connection failed');
}

echo "<h2>Creating Certificate Copies Table</h2>";

try {
    // Create certificate_copies table
    $sql = "CREATE TABLE IF NOT EXISTS certificate_copies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        original_certificate_id INT NOT NULL,
        copy_id VARCHAR(50) UNIQUE NOT NULL,
        copy_format VARCHAR(10) NOT NULL,
        copy_purpose VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_by INT,
        status ENUM('active', 'revoked', 'expired') DEFAULT 'active',
        FOREIGN KEY (original_certificate_id) REFERENCES certificates(certificate_id) ON DELETE CASCADE,
        FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
    )";
    
    $pdo->exec($sql);
    echo "<p>✅ certificate_copies table created successfully!</p>";
    
    // Add indexes for better performance
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_copy_id ON certificate_copies(copy_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_original_cert ON certificate_copies(original_certificate_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_copy_status ON certificate_copies(status)");
    
    echo "<p>✅ Indexes created successfully!</p>";
    
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
    
    echo "<p><strong>✅ Certificate copies functionality is now ready!</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?> 