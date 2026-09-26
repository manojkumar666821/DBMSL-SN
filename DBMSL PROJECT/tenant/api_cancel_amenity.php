<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$userId = $_SESSION['user_id'] ?? 3;
$bookingId = $_POST['booking_id'] ?? null;

if (!$bookingId) {
    echo json_encode(["success" => false, "message" => "Booking ID is required"]);
    exit;
}

$stmt = $conn->prepare("UPDATE amenity_bookings SET booking_status = 'CANCELLED' WHERE booking_id = ? AND tenant_id = ?");
$stmt->bind_param("ii", $bookingId, $userId);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(["success" => true, "message" => "Booking cancelled successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Unable to cancel booking"]);
}