<?php
// Database setup script for CamGovCA
echo "<h2>CamGovCA Database Setup</h2>";

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'camgovca_db';

try {
    // Connect to MySQL without selecting a database
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<p style='color: green;'>✓ Connected to MySQL successfully</p>";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p style='color: green;'>✓ Database '$dbname' created/verified</p>";
    
    // Select the database
    $pdo->exec("USE `$dbname`");
    echo "<p style='color: green;'>✓ Database selected</p>";
    
    // Check if tables already exist
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: orange;'>⚠ Database tables already exist. Skipping schema import.</p>";
    } else {
        // Import schema
        $schema_file = 'camgovca_database_schema.sql';
        if (file_exists($schema_file)) {
            $sql = file_get_contents($schema_file);
            
            // Split SQL into individual statements
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            
            foreach ($statements as $statement) {
                if (!empty($statement) && !preg_match('/^(--|\/\*|USE)/', $statement)) {
                    try {
                        $pdo->exec($statement);
                    } catch (PDOException $e) {
                        // Skip CREATE DATABASE and USE statements
                        if (!preg_match('/CREATE DATABASE|USE /', $statement)) {
                            echo "<p style='color: red;'>Error executing: " . substr($statement, 0, 50) . "...</p>";
                        }
                    }
                }
            }
            echo "<p style='color: green;'>✓ Database schema imported successfully</p>";
        } else {
            echo "<p style='color: red;'>✗ Schema file '$schema_file' not found</p>";
        }
    }
    
    // Import sample data if available
    $data_file = 'camgovca_database_data.sql';
    if (file_exists($data_file)) {
        $sql = file_get_contents($data_file);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        $imported = 0;
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^(--|\/\*)/', $statement)) {
                try {
                    $pdo->exec($statement);
                    $imported++;
                } catch (PDOException $e) {
                    // Skip errors for duplicate entries
                    if (!strpos($e->getMessage(), 'Duplicate entry')) {
                        echo "<p style='color: orange;'>Warning: " . $e->getMessage() . "</p>";
                    }
                }
            }
        }
        echo "<p style='color: green;'>✓ Sample data imported ($imported statements)</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Sample data file '$data_file' not found</p>";
    }
    
    // Test the connection with the new database
    require_once 'config/database.php';
    $test_pdo = getDBConnection();
    if ($test_pdo) {
        echo "<p style='color: green;'>✓ Database connection test successful</p>";
        
        // Show some basic stats
        $tables = ['users', 'certificates', 'faq_entries', 'system_settings'];
        foreach ($tables as $table) {
            try {
                $stmt = $test_pdo->query("SELECT COUNT(*) as count FROM $table");
                $result = $stmt->fetch();
                echo "<p>📊 $table: " . $result['count'] . " records</p>";
            } catch (PDOException $e) {
                echo "<p>📊 $table: Table not found</p>";
            }
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Database setup failed: " . $e->getMessage() . "</p>";
    echo "<p>Please make sure:</p>";
    echo "<ul>";
    echo "<li>MySQL service is running in XAMPP</li>";
    echo "<li>MySQL credentials are correct (default: root, no password)</li>";
    echo "<li>You have sufficient privileges to create databases</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='test_db.php'>Test Database Connection</a> | <a href='index.php'>Go to Homepage</a></p>";
?> 