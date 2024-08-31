<?php
$server="localhost";
$username="txtidc";
$password="vedidc2306";
$database="textidcDB";

// Set the backup directory and filename
$backupDir = 'C:/xampp/htdocs/final_typeIDC/version3/backup/';
$backupFile = $backupDir . 'backup_' . date('Y-m-d') . '.sql';

// Command to export database
$command = "mysqldump -h $dbHost -u $dbUser  $dbName > $backupFile";

// Execute the command
exec($command, $output, $returnValue);

// Check if the backup was successful
if ($returnValue === 0) {
    echo "Backup completed successfully.\n";
} else {
    echo "Error occurred during backup: $returnValue\n";
}
?>
