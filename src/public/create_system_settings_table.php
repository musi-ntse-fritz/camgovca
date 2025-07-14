<?php
require_once 'config/database.php';

$pdo = getDBConnection();
if (!$pdo) {
    die('Database connection failed');
}

echo "<h2>Creating System Settings Table</h2>";

try {
    // Create system_settings table
    $sql = "CREATE TABLE IF NOT EXISTS system_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(255) UNIQUE NOT NULL,
        setting_value TEXT,
        setting_type VARCHAR(50) DEFAULT 'text',
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "<p>✅ system_settings table created successfully!</p>";
    
    // Add indexes for better performance
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_setting_key ON system_settings(setting_key)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_setting_type ON system_settings(setting_type)");
    
    echo "<p>✅ Indexes created successfully!</p>";
    
    // Insert some default settings
    $default_settings = [
        ['site_name', 'CamGovCA', 'text', 'Nom du site'],
        ['site_description', 'Autorité de Certification du Cameroun', 'text', 'Description du site'],
        ['max_certificate_duration', '365', 'number', 'Durée maximale des certificats en jours'],
        ['enable_2fa', 'true', 'boolean', 'Activer l\'authentification à deux facteurs'],
        ['email_notifications', 'true', 'boolean', 'Activer les notifications par email']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO system_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, ?, ?)");
    
    foreach ($default_settings as $setting) {
        $stmt->execute($setting);
    }
    
    echo "<p>✅ Default settings inserted successfully!</p>";
    
    // Check if table exists and show structure
    $stmt = $pdo->query("DESCRIBE system_settings");
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
    
    // Show current settings
    $stmt = $pdo->query("SELECT * FROM system_settings ORDER BY setting_key");
    $settings = $stmt->fetchAll();
    
    echo "<h3>Current Settings:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Key</th><th>Value</th><th>Type</th><th>Description</th></tr>";
    foreach ($settings as $setting) {
        echo "<tr>";
        echo "<td>{$setting['setting_key']}</td>";
        echo "<td>{$setting['setting_value']}</td>";
        echo "<td>{$setting['setting_type']}</td>";
        echo "<td>{$setting['description']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><strong>✅ System settings functionality is now ready!</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?> 