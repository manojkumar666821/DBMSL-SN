<?php
// api/guest/get_flat_details.php
header('Content-Type: application/json');
require_once '../config/DBconnect.php';

$flat_id = isset($_GET['flat_id']) ? (int)$_GET['flat_id'] : 0;

if ($flat_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid flat ID."]);
    exit;
}

$sql = "SELECT f.*, u.full_name AS owner_name, u.phone_number AS owner_phone, u.email AS owner_email,
               ov.status AS deed_status, ov.holding_tax_number
        FROM flats f
        JOIN users u ON f.owner_id = u.user_id
        LEFT JOIN owner_verifications ov ON ov.user_id = u.user_id
        WHERE f.flat_id = $flat_id LIMIT 1";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $flat = $result->fetch_assoc();
    
    // Calculate ledger splits
    $basePrice = (float)$flat['rent_amount'];
    $service   = (float)$flat['service_charge'];
    $utility   = round($service * 0.75, 2);
    $deposit   = ($flat['listing_type'] === 'RENT') ? ($basePrice * 2) : 0;
    $downpayment = ($flat['listing_type'] === 'SALE') ? ($basePrice * 0.10) : 0;

    $response = [
        "success" => true,
        "flat" => [
            "id"              => (int)$flat['flat_id'],
            "unit"            => $flat['flat_number'],
            "block"           => $flat['building_block'],
            "type"            => $flat['listing_type'],
            "sqft"            => (int)$flat['square_feet'],
            "base_price"      => $basePrice,
            "service_charge"  => $service,
            "utility_estimate"=> $utility,
            "deposit"         => $deposit,
            "downpayment"     => $downpayment,
            "total_initial"   => ($flat['listing_type'] === 'RENT') ? ($basePrice + $service + $deposit) : ($basePrice + 200000),
            "owner_name"      => $flat['owner_name'],
            "owner_phone"     => $flat['owner_phone'],
            "deed_verified"   => ($flat['deed_status'] === 'APPROVED'),
            "holding_tax"     => $flat['holding_tax_number'] ?? 'N/A'
        ]
    ];
    echo json_encode($response);
} else {
    echo json_encode(["success" => false, "message" => "Flat not found."]);
}

$conn->close();
?>