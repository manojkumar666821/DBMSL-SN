<?php
// guest_dashboard/get_flats.php
header('Content-Type: application/json');
require_once '../config/DBconnect.php';

$search = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';
$mode   = isset($_GET['mode']) ? $conn->real_escape_string(trim($_GET['mode'])) : 'ALL';
$block  = isset($_GET['block']) ? $conn->real_escape_string(trim($_GET['block'])) : 'ALL';
$budget = isset($_GET['budget']) && is_numeric($_GET['budget']) ? (float)$_GET['budget'] : 0;

$whereClauses = ["f.status = 'AVAILABLE'"];

if (!empty($search)) {
    $whereClauses[] = "(f.flat_number LIKE '%$search%' OR f.building_block LIKE '%$search%')";
}
if ($mode !== 'ALL') {
    $whereClauses[] = "f.listing_type = '$mode'";
}
if ($block !== 'ALL') {
    $whereClauses[] = "f.building_block = '$block'";
}
if ($budget > 0) {
    $whereClauses[] = "f.rent_amount <= $budget";
}

$whereSql = implode(' AND ', $whereClauses);

$sql = "SELECT f.flat_id, f.building_block, f.flat_number, f.square_feet, 
               f.listing_type, f.rent_amount AS base_price, f.service_charge, 
               f.status, u.full_name AS owner_name, u.phone_number AS owner_phone
        FROM flats f
        LEFT JOIN users u ON f.owner_id = u.user_id
        WHERE $whereSql
        ORDER BY f.flat_id DESC";

$result = $conn->query($sql);

$flats = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['photo'] = "https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&auto=format&fit=crop&q=80";
        $row['utility_estimate'] = round((float)$row['service_charge'] * 0.7, 2);
        $row['deposit'] = ($row['listing_type'] === 'RENT') ? ((float)$row['base_price'] * 2) : 0;
        $flats[] = $row;
    }
}

echo json_encode(["success" => true, "total" => count($flats), "data" => $flats]);
$conn->close();
?>