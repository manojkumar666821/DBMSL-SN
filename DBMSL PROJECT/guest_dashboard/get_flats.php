<?php
// ============================================================================
// FILE: guest_dashboard/get_flats.php
// ARCHITECTURE: Resilient Flats Fetcher Engine
// ============================================================================
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);

require_once '../config/DBconnect.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$mode   = isset($_GET['mode']) ? trim($_GET['mode']) : 'ALL';
$block  = isset($_GET['block']) ? trim($_GET['block']) : 'ALL';
$budget = isset($_GET['budget']) ? (float)$_GET['budget'] : 0;

$sql = "SELECT flat_id, building_block, flat_number, square_feet, listing_type, base_price, service_charge, status FROM flats WHERE status = 'AVAILABLE'";
$params = [];
$types  = "";

// 1. Listing Mode Filter (RENT / SALE)
if ($mode !== 'ALL' && !empty($mode)) {
    $sql .= " AND listing_type = ?";
    $params[] = $mode;
    $types .= "s";
}

// 2. Tower Block Filter (Clean 'Block-' prefix so it matches 'A', 'B', 'C' perfectly)
if ($block !== 'ALL' && !empty($block)) {
    $cleanedBlock = trim(str_ireplace('Block-', '', $block));
    $sql .= " AND (building_block = ? OR building_block = ?)";
    $params[] = $cleanedBlock;
    $params[] = "Block-" . $cleanedBlock;
    $types .= "ss";
}

// 3. Budget Filter
if ($budget > 0) {
    $sql .= " AND base_price <= ?";
    $params[] = $budget;
    $types .= "d";
}

// 4. Search Filter
if (!empty($search)) {
    $sql .= " AND (flat_number LIKE ? OR building_block LIKE ?)";
    $like = "%" . $search . "%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$flats = [];
$defaultPhotos = [
    'RENT' => [
        'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&auto=format&fit=crop&q=80',
        'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&auto=format&fit=crop&q=80',
        'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800&auto=format&fit=crop&q=80'
    ],
    'SALE' => [
        'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800&auto=format&fit=crop&q=80',
        'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&auto=format&fit=crop&q=80',
        'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&auto=format&fit=crop&q=80'
    ]
];

$i = 0;
while ($row = $result->fetch_assoc()) {
    $type = strtoupper($row['listing_type']);
    $photos = $defaultPhotos[$type] ?? $defaultPhotos['RENT'];
    $row['photo'] = $photos[$i % count($photos)];
    
    // Normalize format for frontend
    $cleanB = str_ireplace('Block-', '', $row['building_block']);
    $row['building_block'] = "Block-" . $cleanB;
    $row['flat_number'] = "Unit " . $cleanB . "-" . $row['flat_number'];

    $flats[] = $row;
    $i++;
}

echo json_encode(['success' => true, 'data' => $flats]);
exit();