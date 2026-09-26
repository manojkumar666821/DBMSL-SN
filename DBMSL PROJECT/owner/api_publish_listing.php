<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$userId = $_SESSION['user_id'] ?? 2;
$unit = trim($_POST['unit'] ?? '');
$type = trim($_POST['type'] ?? 'RENT');
$price = (float)($_POST['price'] ?? 0);

if (empty($unit) || $price <= 0) {
    echo json_encode(["success" => false, "message" => "Unit and valid price are required."]);
    exit;
}

$flatNum = str_replace(['Unit ', ' '], '', explode('(', $unit)[0]);

if ($type === 'SALE') {
    $stmt = $conn->prepare("UPDATE flats SET is_for_sale = 1, is_for_rent = 0, sale_price = ?, flat_status = 'AVAILABLE' WHERE flat_number = ? AND owner_id = ?");
} else {
    $stmt = $conn->prepare("UPDATE flats SET is_for_rent = 1, is_for_sale = 0, rent_price = ?, flat_status = 'AVAILABLE' WHERE flat_number = ? AND owner_id = ?");
}
$stmt->bind_param("dsi", $price, $flatNum, $userId);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Unit {$flatNum} published to marketplace."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update listing."]);
}