<?php
require_once 'config/database.php';

$pdo = getDBConnection();
if (!$pdo) {
    die('Database connection failed');
}

echo "<h2>Checking Certificates Table Structure</h2>";

try {
    // Check if certificates table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'certificates'");
    $table_exists = $stmt->rowCount() > 0;
    echo "<p><strong>certificates table exists:</strong> " . ($table_exists ? 'YES' : 'NO') . "</p>";
    
    if ($table_exists) {
        // Show certificates table structure
        $stmt = $pdo->query("DESCRIBE certificates");
        $columns = $stmt->fetchAll();
        
        echo "<h3>Certificates Table Structure:</h3>";
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
        
        // Check users table structure
        $stmt = $pdo->query("DESCRIBE users");
        $user_columns = $stmt->fetchAll();
        
        echo "<h3>Users Table Structure:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($user_columns as $column) {
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
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?> 