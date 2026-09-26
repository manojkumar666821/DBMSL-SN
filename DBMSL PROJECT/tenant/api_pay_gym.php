<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$userId = $_SESSION['user_id'] ?? 3;
$planAmount = (float)($_POST['plan_amount'] ?? 1500);
$shift = trim($_POST['shift'] ?? 'Morning (06:00 AM – 11:00 AM)');
$method = trim($_POST['pay_method'] ?? 'bKash Merchant');
$trxId = trim($_POST['trx_id'] ?? '');

if (empty($trxId)) {
    echo json_encode(["success" => false, "message" => "TrxID is required"]);
    exit;
}

$startDate = date('Y-m-d');
$endDate = ($planAmount >= 14000) ? date('Y-m-d', strtotime('+1 year')) : (($planAmount >= 4000) ? date('Y-m-d', strtotime('+3 months')) : date('Y-m-d', strtotime('+1 month')));

// Updated query with payment method and TrxID
$stmt = $conn->prepare("INSERT INTO amenity_bookings (amenity_id, tenant_id, booking_date, start_time, end_time, booking_status, payment_method, transaction_reference, created_at) VALUES (1, ?, ?, '06:00:00', '23:00:00', 'CONFIRMED', ?, ?, NOW())");
$stmt->bind_param("isss", $userId, $startDate, $method, $trxId);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Gym membership activated successfully",
        "membership_id" => "GYM-" . $stmt->insert_id,
        "valid_until" => $endDate
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Gym enrollment failed"]);
}