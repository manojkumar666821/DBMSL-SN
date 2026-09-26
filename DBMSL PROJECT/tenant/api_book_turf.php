<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$userId = $_SESSION['user_id'] ?? 3;
$date = trim($_POST['play_date'] ?? '');
$timeSlot = trim($_POST['time_slot'] ?? '');
$method = trim($_POST['pay_method'] ?? 'bKash Online Gateway');
$trxId = trim($_POST['trx_id'] ?? '');

if (empty($date) || empty($timeSlot) || empty($trxId)) {
    echo json_encode(["success" => false, "message" => "Date, Slot and TrxID are required"]);
    exit;
}

$start = explode('–', $timeSlot)[0];
$startTime = trim($start);
$endTime = trim(explode('(', explode('–', $timeSlot)[1] ?? '')[0]);

if (strlen($startTime) <= 5) $startTime .= ':00';
if (strlen($endTime) <= 5) $endTime .= ':00';

$check = $conn->prepare("SELECT booking_id FROM amenity_bookings WHERE amenity_id = 2 AND booking_date = ? AND start_time = ? AND booking_status IN ('CONFIRMED', 'PENDING')");
$check->bind_param("ss", $date, $startTime);
$check->execute();
if ($check->get_result()->fetch_assoc()) {
    echo json_encode(["success" => false, "message" => "This turf slot is already booked"]);
    exit;
}

// Updated query with payment method and TrxID
$stmt = $conn->prepare("INSERT INTO amenity_bookings (amenity_id, tenant_id, booking_date, start_time, end_time, booking_status, payment_method, transaction_reference, created_at) VALUES (2, ?, ?, ?, ?, 'CONFIRMED', ?, ?, NOW())");
$stmt->bind_param("isssss", $userId, $date, $startTime, $endTime, $method, $trxId);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Turf slot reserved and paid",
        "booking_ref" => "TRF-" . $stmt->insert_id
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to book turf"]);
}