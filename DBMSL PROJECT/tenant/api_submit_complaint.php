<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$userId = $_SESSION['user_id'] ?? 3;
$flatId = (int)($_POST['flat_id'] ?? 2);
$category = trim($_POST['category'] ?? 'PLUMBING');
$scope = trim($_POST['scope'] ?? 'IN_FLAT');
$subject = trim($_POST['subject'] ?? '');
$summary = trim($_POST['problem_summary'] ?? '');

$problemText = !empty($subject) ? ($subject . ": " . $summary) : $summary;

if (empty($problemText)) {
    echo json_encode(["success" => false, "message" => "Problem summary is required"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO maintenance_complaints (flat_id, tenant_id, complaint_type, scope, description, status, created_at) VALUES (?, ?, ?, ?, ?, 'PENDING', NOW())");
$stmt->bind_param("iisss", $flatId, $userId, $category, $scope, $problemText);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Complaint logged successfully",
        "ticket_id" => "TKT-" . $stmt->insert_id
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to log complaint"]);
}