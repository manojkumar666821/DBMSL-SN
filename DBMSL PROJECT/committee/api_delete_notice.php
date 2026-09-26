<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$noticeId = (int)($_POST['notice_id'] ?? 0);

if ($noticeId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid notice ID.']);
    exit();
}

$stmt = $conn->prepare("DELETE FROM notices WHERE notice_id = ?");
$stmt->bind_param("i", $noticeId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Notice archived successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}
$stmt->close();