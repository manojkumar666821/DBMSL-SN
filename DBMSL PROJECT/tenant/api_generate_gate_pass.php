<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$userId = $_SESSION['user_id'] ?? 3;
$name = trim($_POST['visitor_name'] ?? '');
$phone = trim($_POST['phone_number'] ?? '');
$flatId = (int)($_POST['flat_id'] ?? 2);

if (empty($name) || empty($phone)) {
    echo json_encode(["success" => false, "message" => "Name and phone are required"]);
    exit;
}

$rawOtp = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

$stmt = $conn->prepare("INSERT INTO gate_visitors (flat_id, tenant_id, visitor_name, visitor_phone, gate_pass_otp, is_used, created_at) VALUES (?, ?, ?, ?, ?, 0, NOW())");
$stmt->bind_param("iisss", $flatId, $userId, $name, $phone, $rawOtp);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Gate pass created",
        "pass" => [
            "pass_id" => $stmt->insert_id,
            "otp" => substr($rawOtp, 0, 3) . '-' . substr($rawOtp, 3, 3)
        ]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Gate pass generation failed"]);
}