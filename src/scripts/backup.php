<?php
$backup_dir = '../backups/';
if (!is_dir($backup_dir)) mkdir($backup_dir, 0777, true);
$filename = 'camgovca_backup_' . date('Y-m-d_H-i-s') . '.sql';
$command = "mysqldump -u camgovca_app -p'your_secure_password' camgovca_db > " . $backup_dir . $filename;
system($command);
echo "Backup created: " . $filename;
?> 