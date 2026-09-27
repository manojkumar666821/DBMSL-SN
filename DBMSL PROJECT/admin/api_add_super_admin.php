<?php
// admin/api_add_super_admin.php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

// Strict Super Admin Access Verification
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
    echo json_encode(["success" => false, "message" => "Unauthorized access."]);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

$fullName       = trim($input['name'] ?? '');
$email          = trim($input['email'] ?? '');
$clearanceLevel = trim($input['clearance'] ?? 'ROOT');
$secretKey      = trim($input['secret_key'] ?? '');

$masterKey = "GREENVIEW_ROOT_2026";

if ($secretKey !== $masterKey) {
    echo json_encode(["success" => false, "message" => "Invalid Root Authorization Key! Use: " . $masterKey]);
    exit();
}

if (empty($fullName) || empty($email)) {
    echo json_encode(["success" => false, "message" => "Name and official email are required."]);
    exit();
}

// Check duplicate
$chk = $conn->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
$chk->bind_param("s", $email);
$chk->execute();
if ($chk->get_result()->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "This email is already registered."]);
    exit();
}
$chk->close();

// Default admin pass 12345678 (hash)
$defaultPass = "12345678";
$passwordHash = password_hash($defaultPass, PASSWORD_BCRYPT);
$phone = "0181" . random_int(1000000, 9999999);
$roleId = 6; // SYSADMIN / ADMIN

$stmt = $conn->prepare("INSERT INTO users (role_id, full_name, email, phone_number, password_hash, is_verified, is_active, created_at) VALUES (?, ?, ?, ?, ?, 1, 1, NOW())");
$stmt->bind_param("issss", $roleId, $fullName, $email, $phone, $passwordHash);

if ($stmt->execute()) {
    $newId = $stmt->insert_id;
    echo json_encode([
        "success" => true,
        "message" => "Super Admin '{$fullName}' created successfully in database!",
        "user_id" => $newId
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Database insert error: " . $conn->error]);
}
$stmt->close();
?>