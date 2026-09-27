<?php
// admin/api_get_audit_logs.php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized."]);
    exit();
}

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;

$stmt = $conn->prepare("SELECT log_id, user_id, action_name, target_table, record_id, ip_address, logged_at FROM audit_logs ORDER BY log_id DESC LIMIT ?");
$stmt->bind_param("i", $limit);
$stmt->execute();
$result = $stmt->get_result();
$logs = [];

while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}
$stmt->close();

echo json_encode([
    "status" => "success",
    "total"  => count($logs),
    "data"   => $logs
]);
?>