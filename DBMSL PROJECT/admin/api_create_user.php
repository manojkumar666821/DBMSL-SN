<?php
// admin/api_create_user.php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized session."]);
    exit();
}

// JSON Payload receive
$input = json_decode(file_get_contents('php://input'), true);

$fullName = trim($input['full_name'] ?? '');
$email    = trim($input['email'] ?? '');
$phone    = trim($input['phone_number'] ?? '');
$roleId   = (int)($input['role_id'] ?? 2);
$password = trim($input['password'] ?? '');

if (empty($fullName) || empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Name, Email and Password are required."]);
    exit();
}

if (strlen($password) < 8) {
    echo json_encode(["success" => false, "message" => "Password must be at least 8 characters."]);
    exit();
}

// Check duplicate email
$chk = $conn->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
$chk->bind_param("s", $email);
$chk->execute();
if ($chk->get_result()->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email already registered in system."]);
    exit();
}
$chk->close();

$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO users (full_name, email, phone_number, password_hash, role_id, is_active, created_at) VALUES (?, ?, ?, ?, ?, 1, NOW())");
$stmt->bind_param("ssssi", $fullName, $email, $phone, $hash, $roleId);

if ($stmt->execute()) {
    $newId = $stmt->insert_id;
    
    // Log audit trail
    $adminId = (int)$_SESSION['user_id'];
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $log = $conn->prepare("INSERT INTO audit_logs (user_id, action_name, target_table, record_id, ip_address, logged_at) VALUES (?, 'PROVISION_ACCOUNT', 'users', ?, ?, NOW())");
    $log->bind_param("iiss", $adminId, $newId, $ip);
    $log->execute();
    $log->close();

    echo json_encode([
        "success" => true,
        "message" => "Account provisioned successfully!",
        "user_id" => $newId
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to create user: " . $conn->error]);
}
$stmt->close();
?>