<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$userId = $_SESSION['user_id'] ?? 3;

$notices = $conn->query("SELECT notice_id, title, content, target_role, created_at FROM notices WHERE target_role IN ('ALL', 'TENANT') ORDER BY notice_id DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

$stmt = $conn->prepare("SELECT complaint_id, description, status FROM maintenance_complaints WHERE tenant_id = ? AND status IN ('PENDING', 'RESOLVED') ORDER BY complaint_id DESC LIMIT 5");
$stmt->bind_param("i", $userId);
$stmt->execute();
$complaintAlerts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    "success" => true,
    "data" => [
        "notices" => $notices,
        "complaint_alerts" => $complaintAlerts
    ]
]);