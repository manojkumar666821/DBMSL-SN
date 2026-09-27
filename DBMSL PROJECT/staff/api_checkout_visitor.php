<?php
session_start();
date_default_timezone_set('Asia/Dhaka');
require_once __DIR__ . '/../config/DBconnect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || (int)($_SESSION['role_id'] ?? 0) !== 5) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$visitorId = (int)($_POST['visitor_id'] ?? 0);
if ($visitorId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid Visitor ID.']);
    exit();
}

$stmt = $conn->prepare("UPDATE gate_visitors SET exit_time = NOW() WHERE visitor_id = ?");
$stmt->bind_param("i", $visitorId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to log checkout.']);
}