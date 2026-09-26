<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$userId = $_SESSION['user_id'] ?? 2;
$category = trim($_POST['category'] ?? 'STRUCTURAL');
$unit = trim($_POST['unit'] ?? 'Unit A-401');
$summary = trim($_POST['summary'] ?? '');

if (empty($summary)) {
    echo json_encode(["success" => false, "message" => "Summary is required."]);
    exit;
}

$flatNum = str_replace(['Unit ', ' '], '', explode('(', $unit)[0]);
$flat = $conn->query("SELECT flat_id FROM flats WHERE flat_number = '{$flatNum}' LIMIT 1")->fetch_assoc();
$flatId = $flat ? $flat['flat_id'] : 1;

$stmt = $conn->prepare("INSERT INTO maintenance_complaints (flat_id, tenant_id, complaint_type, scope, description, status, created_at) VALUES (?, ?, ?, 'COMMON_AREA', ?, 'PENDING', NOW())");
$stmt->bind_param("iiss", $flatId, $userId, $category, $summary);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Complaint successfully filed with the managing committee.",
        "ticket_id" => "OWN-TKT-" . $stmt->insert_id
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to record complaint."]);
}