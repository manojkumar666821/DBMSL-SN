<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$action = trim($_POST['action'] ?? '');
$verificationId = (int)($_POST['verification_id'] ?? 0);
$userId = (int)($_SESSION['user_id'] ?? 1);

if ($verificationId <= 0 || !in_array($action, ['APPROVE', 'REJECT'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
    exit();
}

$status = ($action === 'APPROVE') ? 'APPROVED' : 'REJECTED';

$stmt = $conn->prepare("UPDATE owner_verifications SET status = ?, approved_by = ?, verified_at = NOW() WHERE verification_id = ?");
$stmt->bind_param("sii", $status, $userId, $verificationId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => "Deed has been {$status}."]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}
$stmt->close();