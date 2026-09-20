<?php
// api/guest/get_amenities.php
header('Content-Type: application/json');
require_once '../config/DBconnect.php';

$sql = "SELECT amenity_id, amenity_name, hourly_rate, 
               TIME_FORMAT(opening_time, '%h:%i %p') AS open_time, 
               TIME_FORMAT(closing_time, '%h:%i %p') AS close_time, 
               is_active
        FROM society_amenities 
        WHERE is_active = 1
        ORDER BY amenity_id ASC";

$result = $conn->query($sql);
$amenities = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $amenities[] = $row;
    }
}

echo json_encode(["success" => true, "data" => $amenities]);
$conn->close();
?>