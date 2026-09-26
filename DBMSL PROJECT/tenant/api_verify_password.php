<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$currentPassword = trim($_POST['current_password'] ?? '');

if ($userId <= 0) {
    echo json_encode(["success" => false, "message" => "Session expired. Please log in again."]);
    exit();
}

if (empty($currentPassword)) {
    echo json_encode(["success" => false, "message" => "Current password is required."]);
    exit();
}

$stmt = $conn->prepare("SELECT password_hash FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo json_encode(["success" => false, "message" => "User account not found."]);
    exit();
}

$isValid = false;
if (password_verify($currentPassword, $user['password_hash']) || $currentPassword === $user['password_hash']) {
    $isValid = true;
}

if (!$isValid) {
    echo json_encode(["success" => false, "message" => "Invalid current password."]);
    exit();
}

$_SESSION['pwd_verified'] = true;
echo json_encode(["success" => true, "message" => "Identity verified. Proceed to Step 2."]);