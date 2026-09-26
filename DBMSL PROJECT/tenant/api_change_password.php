<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if ($userId <= 0) {
    echo json_encode(["success" => false, "message" => "Session expired. Please log in again."]);
    exit();
}

if (empty($_SESSION['pwd_verified'])) {
    echo json_encode(["success" => false, "message" => "Please complete verification first."]);
    exit();
}

if (strlen($newPassword) < 8) {
    echo json_encode(["success" => false, "message" => "Password must be at least 8 characters."]);
    exit();
}

if ($newPassword !== $confirmPassword) {
    echo json_encode(["success" => false, "message" => "Passwords do not match."]);
    exit();
}

$hash = password_hash($newPassword, PASSWORD_BCRYPT);
$stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
$stmt->bind_param("si", $hash, $userId);

if ($stmt->execute()) {
    unset($_SESSION['pwd_verified']);
    echo json_encode(["success" => true, "message" => "Password changed successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Password change failed: " . $conn->error]);
}
$stmt->close();