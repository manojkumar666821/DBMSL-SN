<?php
// admin/api_create_backup.php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized."]);
    exit();
}

$backupDir = __DIR__ . '/../backups/';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

$fileName = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
$filePath = $backupDir .$fileName;

$tables = [];
$result =$conn->query("SHOW TABLES");
while ($row =$result->fetch_row()) {
    $tables[] =$row[0];
}

$sqlDump = "-- Greenview Heights Database Snapshot\n";
$sqlDump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";

foreach ($tables as$table) {
    $res =$conn->query("SHOW CREATE TABLE `{$table}`");
    $createTable =$res->fetch_row();
    $sqlDump .= "\n\n" . $createTable[1] . ";\n\n";

    $dataRes =$conn->query("SELECT * FROM `{$table}`");
    $columnCount =$dataRes->field_count;

    while ($row = $dataRes->fetch_row()) {$sqlDump .= "INSERT INTO `{$table}` VALUES(";
        for ($j = 0; $j < $columnCount; $j++) {
            if (isset($row[$j])) {
                $sqlDump .= "'" . $conn->real_escape_string($row[$j]) . "'";
            } else {
                $sqlDump .= "NULL";
            }
            if ($j < ($columnCount - 1)) {$sqlDump .= ",";
            }
        }
        $sqlDump .= ");\n";
    }
}

if (file_put_contents($filePath,$sqlDump)) {
    // Audit Log
    $adminId = (int)$_SESSION['user_id'];
    $ip =$_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $log =$conn->prepare("INSERT INTO audit_logs (user_id, action_name, target_table, record_id, ip_address, logged_at) VALUES (?, 'DATABASE_BACKUP', 'system', 0, ?, NOW())");
    $log->bind_param("is", $adminId,$ip);
    $log->execute();$log->close();

    echo json_encode([
        "status"    => "success",
        "message"   => "Database snapshot successfully created!",
        "file_name" => $fileName
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to write backup dump file."]);
}
?>