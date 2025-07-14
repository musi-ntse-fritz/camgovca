<?php
/**
 * Fix Suspension Columns Script
 * This script adds the missing suspension-related columns to the certificates table
 */

require_once 'config/database.php';

echo "<h2>Fixing Suspension Columns</h2>";

try {
    $pdo = getDBConnection();
    
    // Check if suspended_at column already exists
    $checkColumn = $pdo->query("SHOW COLUMNS FROM certificates LIKE 'suspended_at'");
    $columnExists = $checkColumn->rowCount() > 0;
    
    if ($columnExists) {
        echo "<p style='color: green;'>✓ Suspension columns already exist. No action needed.</p>";
    } else {
        echo "<p>Adding suspension columns to certificates table...</p>";
        
        // Add suspension-related columns
        $sql = "
        ALTER TABLE certificates 
        ADD COLUMN suspended_at TIMESTAMP NULL,
        ADD COLUMN suspension_reason VARCHAR(500) NULL,
        ADD COLUMN suspension_end_date TIMESTAMP NULL,
        ADD COLUMN resumed_at TIMESTAMP NULL,
        ADD COLUMN resume_reason VARCHAR(500) NULL,
        ADD COLUMN resumed_by INT NULL,
        ADD COLUMN suspended_by INT NULL,
        ADD INDEX idx_suspended_at (suspended_at),
        ADD INDEX idx_suspension_end_date (suspension_end_date),
        ADD INDEX idx_resumed_at (resumed_at),
        ADD INDEX idx_suspended_by (suspended_by),
        ADD INDEX idx_resumed_by (resumed_by)
        ";
        
        $pdo->exec($sql);
        echo "<p style='color: green;'>✓ Suspension columns added successfully!</p>";
        
        // Add foreign key constraints
        try {
            $pdo->exec("ALTER TABLE certificates ADD FOREIGN KEY (suspended_by) REFERENCES users(user_id)");
            $pdo->exec("ALTER TABLE certificates ADD FOREIGN KEY (resumed_by) REFERENCES users(user_id)");
            echo "<p style='color: green;'>✓ Foreign key constraints added successfully!</p>";
        } catch (Exception $e) {
            echo "<p style='color: orange;'>⚠ Foreign key constraints could not be added: " . $e->getMessage() . "</p>";
        }
        
        // Add suspension-related settings
        $settings = [
            ['certificate_suspension_max_duration_days', '30', 'integer', 'Maximum suspension duration in days'],
            ['certificate_suspension_require_reason', 'true', 'boolean', 'Require reason for certificate suspension'],
            ['certificate_resume_require_reason', 'true', 'boolean', 'Require reason for certificate resume'],
            ['certificate_suspension_auto_notify', 'true', 'boolean', 'Automatically notify users of certificate suspension'],
            ['certificate_resume_auto_notify', 'true', 'boolean', 'Automatically notify users of certificate resume']
        ];
        
        foreach ($settings as $setting) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO system_settings (setting_key, setting_value, setting_type, description, is_public) 
                    VALUES (?, ?, ?, ?, true)
                    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
                ");
                $stmt->execute($setting);
            } catch (Exception $e) {
                echo "<p style='color: orange;'>⚠ Could not add setting {$setting[0]}: " . $e->getMessage() . "</p>";
            }
        }
        
        echo "<p style='color: green;'>✓ System settings added successfully!</p>";
    }
    
    echo "<p><strong>Fix completed successfully!</strong></p>";
    echo "<p><a href='reprendre-certificat.php'>← Back to Certificate Resume Page</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database connection and try again.</p>";
}
?> 