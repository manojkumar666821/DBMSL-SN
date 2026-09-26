<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$complaintId = (int)($_POST['complaint_id'] ?? 0);

if ($complaintId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid complaint ID.']);
    exit();
}

$stmt = $conn->prepare("UPDATE maintenance_complaints SET status = 'RESOLVED', resolved_at = NOW() WHERE complaint_id = ?");
$stmt->bind_param("i", $complaintId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Complaint marked as resolved.']);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}
$stmt->close();