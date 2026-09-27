<?php
// admin/api_update_user_role.php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized."]);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$targetUserId = (int)($input['user_id'] ?? 0);

if ($targetUserId <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid user ID."]);
    exit();
}

$adminId = (int)$_SESSION['user_id'];
$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

// Case A: Role shift
if (isset($input['role_id'])) {
    $newRoleId = (int)$input['role_id'];
    $stmt = $conn->prepare("UPDATE users SET role_id = ? WHERE user_id = ?");
    $stmt->bind_param("ii", $newRoleId, $targetUserId);
    $stmt->execute();
    $stmt->close();

    // Audit log
    $log = $conn->prepare("INSERT INTO audit_logs (user_id, action_name, target_table, record_id, ip_address, logged_at) VALUES (?, 'UPDATE_USER_ROLE', 'users', ?, ?, NOW())");
    $log->bind_param("iiss", $adminId, $targetUserId, $ip);
    $log->execute();
    $log->close();

    echo json_encode(["success" => true, "message" => "User role updated successfully."]);
    exit();
}

// Case B: Access Toggle (Revoke / Restore)
if (isset($input['is_active'])) {
    $isActive = (int)$input['is_active'];
    $stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE user_id = ?");
    $stmt->bind_param("ii", $isActive, $targetUserId);
    $stmt->execute();
    $stmt->close();

    $actionName = $isActive ? 'RESTORE_ACCESS' : 'REVOKE_ACCESS';
    $log = $conn->prepare("INSERT INTO audit_logs (user_id, action_name, target_table, record_id, ip_address, logged_at) VALUES (?, ?, 'users', ?, ?, NOW())");
    $log->bind_param("isiss", $adminId, $actionName, $targetUserId, $ip);
    $log->execute();
    $log->close();

    echo json_encode(["success" => true, "message" => "Access state updated successfully."]);
    exit();
}

echo json_encode(["success" => false, "message" => "No valid action parameters provided."]);
?>