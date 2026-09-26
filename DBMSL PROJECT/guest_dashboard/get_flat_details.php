<?php
// ============================================================================
// FILE: guest_dashboard/get_flat_details.php
// ============================================================================
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);

require_once '../config/DBconnect.php';

$flat_id = isset($_GET['flat_id']) ? (int)$_GET['flat_id'] : 0;

if ($flat_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid Flat ID']);
    exit();
}

$stmt = $conn->prepare("SELECT * FROM flats WHERE flat_id = ? LIMIT 1");
$stmt->bind_param("i", $flat_id);
$stmt->execute();
$res = $stmt->get_result();

if ($flat = $res->fetch_assoc()) {
    $isRent = (strtoupper($flat['listing_type']) === 'RENT');
    $basePrice = (float)$flat['base_price'];
    $serviceCharge = (float)$flat['service_charge'];
    
    // Auto-calculate financial ledger breakdown
    $deposit = $isRent ? ($basePrice * 2) : 0;
    $utilityEstimate = $isRent ? 3500.00 : 0;
    $downpayment = $isRent ? 0 : ($basePrice * 0.10);
    $cleanBlock = str_ireplace('Block-', '', $flat['building_block']);

    echo json_encode([
        'success' => true,
        'flat' => [
            'id'               => (int)$flat['flat_id'],
            'unit'             => "Unit " . $cleanBlock . "-" . $flat['flat_number'],
            'block'            => "Block-" . $cleanBlock,
            'sqft'             => (int)$flat['square_feet'],
            'listing_type'     => strtoupper($flat['listing_type']),
            'base_price'       => $basePrice,
            'service_charge'   => $serviceCharge,
            'utility_estimate' => $utilityEstimate,
            'deposit'          => $deposit,
            'downpayment'      => $downpayment,
            'total_initial'    => ($basePrice + $serviceCharge + $utilityEstimate + $deposit)
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Flat record not found']);
}
exit();