<?php
// admin/api_get_users.php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}

$sql = "SELECT u.user_id, u.full_name, u.email, u.phone_number, u.role_id, r.role_name, 
               IFNULL(u.is_active, 1) as is_active 
        FROM users u 
        LEFT JOIN roles r ON u.role_id = r.role_id 
        ORDER BY u.user_id ASC";

$result = $conn->query($sql);
$users = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

echo json_encode([
    "status" => "success",
    "total"  => count($users),
    "data"   => $users
]);
?>