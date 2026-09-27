<?php
session_start();
date_default_timezone_set('Asia/Dhaka');
require_once __DIR__ . '/../config/DBconnect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || (int)($_SESSION['role_id'] ?? 0) !== 5) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$name    = trim($_POST['visitor_name'] ?? '');
$phone   = trim($_POST['visitor_phone'] ?? '');
$flatId  = (int)($_POST['flat_id'] ?? 1);
$tenantId = 3; // Default resident host ID from database
$passOtp = rand(100000, 999999);

if (empty($name) || empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit();
}

$stmt = $conn->prepare("
    INSERT INTO gate_visitors (flat_id, tenant_id, visitor_name, visitor_phone, gate_pass_otp, entry_time, is_used) 
    VALUES (?, ?, ?, ?, ?, NOW(), 1)
");
$stmt->bind_param("iisss", $flatId, $tenantId, $name, $phone, $passOtp);

if ($stmt->execute()) {
    // Fetch building block and flat number for UI display
    $fQuery = $conn->query("SELECT building_block, flat_number FROM flats WHERE flat_id = {$flatId} LIMIT 1");
    $fData = $fQuery ? $fQuery->fetch_assoc() : null;
    $unitLabel = $fData ? "Unit " . $fData['building_block'] . "-" . $fData['flat_number'] : "Unit Common";

    echo json_encode([
        'success'      => true,
        'visitor_id'   => $stmt->insert_id,
        'visitor_name' => $name,
        'phone'        => $phone,
        'unit'         => $unitLabel,
        'otp'          => $passOtp,
        'time'         => date('Y-m-d H:i:s')
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to record walk-in visitor.']);
}