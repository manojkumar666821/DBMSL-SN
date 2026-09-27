<?php
// admin/api_verify_owner.php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized."]);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$verifId = (int)($input['verification_id'] ?? 0);
$status  = strtoupper(trim($input['status'] ?? ''));

if ($verifId <= 0 || !in_array($status, ['VERIFIED', 'REJECTED'])) {
    echo json_encode(["success" => false, "message" => "Invalid verification parameters."]);
    exit();
}

$stmt = $conn->prepare("UPDATE owner_verifications SET status = ?, verified_at = NOW() WHERE id = ?");
$stmt->bind_param("si", $status, $verifId);

if ($stmt->execute()) {
    $adminId = (int)$_SESSION['user_id'];
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $action = ($status === 'VERIFIED') ? 'APPROVE_DEED' : 'REJECT_DEED';

    $log = $conn->prepare("INSERT INTO audit_logs (user_id, action_name, target_table, record_id, ip_address, logged_at) VALUES (?, ?, 'owner_verifications', ?, ?, NOW())");
    $log->bind_param("isiss", $adminId, $action, $verifId, $ip);
    $log->execute();
    $log->close();

    echo json_encode(["success" => true, "message" => "Deed marked as {$status}."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update deed verification."]);
}
$stmt->close();
?>