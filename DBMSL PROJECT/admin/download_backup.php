<?php
// admin/download_backup.php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized.");
}

$file = basename($_GET['file'] ?? '');
$filePath = __DIR__ . '/../backups/' . $file;

if (!empty($file) && file_exists($filePath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $file . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
} else {
    die("Backup file not found.");
}
?>