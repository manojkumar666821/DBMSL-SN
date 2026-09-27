<?php
// admin/api_get_verifications.php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized."]);
    exit();
}

$sql = "SELECT v.id as verification_id, v.user_id, u.full_name as owner_name, 
               v.flat_number, v.area_sqft, v.document_path, v.status, v.created_at
        FROM owner_verifications v
        LEFT JOIN users u ON v.user_id = u.user_id
        ORDER BY v.id DESC";

$result = $conn->query($sql);
$verifications = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $verifications[] = $row;
    }
}

echo json_encode([
    "status" => "success",
    "total"  => count($verifications),
    "data"   => $verifications
]);
?>