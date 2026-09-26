<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$userId = $_SESSION['user_id'] ?? 3;
$date = trim($_POST['event_date'] ?? '');
$shift = trim($_POST['event_shift'] ?? '');
$guests = (int)($_POST['guest_count'] ?? 120);
$method = trim($_POST['pay_method'] ?? 'City Bank Direct Wire');
$trxId = trim($_POST['trx_id'] ?? '');

if (empty($date) || empty($shift) || empty($trxId)) {
    echo json_encode(["success" => false, "message" => "Date, Shift and TrxID are required"]);
    exit;
}

$startTime = strpos($shift, 'Morning') !== false ? '10:00:00' : '18:00:00';
$endTime = strpos($shift, 'Morning') !== false ? '15:00:00' : '23:00:00';

// Updated query with payment method and TrxID
$stmt = $conn->prepare("INSERT INTO amenity_bookings (amenity_id, tenant_id, booking_date, start_time, end_time, booking_status, payment_method, transaction_reference, created_at) VALUES (3, ?, ?, ?, ?, 'CONFIRMED', ?, ?, NOW())");
$stmt->bind_param("isssss", $userId, $date, $startTime, $endTime, $method, $trxId);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Banquet Hall reserved successfully",
        "reservation_ref" => "HAL-" . $stmt->insert_id
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Hall reservation failed"]);
}