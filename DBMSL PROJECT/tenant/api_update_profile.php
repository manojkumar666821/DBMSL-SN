<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$userId = $_SESSION['user_id'] ?? 3;
$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$phone = trim($_POST['phone_number'] ?? '');

if (empty($firstName) || empty($phone)) {
    echo json_encode(["success" => false, "message" => "First name and phone are required"]);
    exit;
}

$fullName = trim($firstName . ' ' . $lastName);

$stmt = $conn->prepare("UPDATE users SET full_name = ?, phone_number = ? WHERE user_id = ?");
$stmt->bind_param("ssi", $fullName, $phone, $userId);

if ($stmt->execute()) {
    $_SESSION['full_name'] = $fullName;
    $_SESSION['phone_number'] = $phone;
    echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update profile"]);
}