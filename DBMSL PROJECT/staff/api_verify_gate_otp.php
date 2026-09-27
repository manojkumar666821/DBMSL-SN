<?php
session_start();
date_default_timezone_set('Asia/Dhaka');
require_once __DIR__ . '/../config/DBconnect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || (int)($_SESSION['role_id'] ?? 0) !== 5) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized gate terminal access.']);
    exit();
}

$otp = trim($_POST['otp'] ?? '');
$cleanOtp = preg_replace('/[^0-9]/', '', $otp);

if (empty($cleanOtp)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid OTP.']);
    exit();
}

// 1. Check in viewing_bookings
$stmtView = $conn->prepare("
    SELECT vb.booking_id, vb.flat_id, vb.guest_id, u.full_name, u.phone_number, f.building_block, f.flat_number 
    FROM viewing_bookings vb
    JOIN users u ON vb.guest_id = u.user_id
    JOIN flats f ON vb.flat_id = f.flat_id
    WHERE vb.gate_pass_otp = ?
    LIMIT 1
");
$stmtView->bind_param("s", $cleanOtp);
$stmtView->execute();
$viewRes = $stmtView->get_result();

if ($row = $viewRes->fetch_assoc()) {
    $tenantId = (int)$row['guest_id'];
    $ins = $conn->prepare("
        INSERT INTO gate_visitors (flat_id, tenant_id, visitor_name, visitor_phone, gate_pass_otp, entry_time, is_used) 
        VALUES (?, ?, ?, ?, ?, NOW(), 1)
    ");
    $ins->bind_param("iisss", $row['flat_id'], $tenantId, $row['full_name'], $row['phone_number'], $cleanOtp);
    $ins->execute();
    $newVisitorId = $ins->insert_id;

    echo json_encode([
        'success'      => true,
        'visitor_id'   => $newVisitorId,
        'visitor_name' => $row['full_name'],
        'phone'        => $row['phone_number'],
        'unit'         => "Unit " . $row['building_block'] . "-" . $row['flat_number'],
        'time'         => date('Y-m-d H:i:s')
    ]);
    exit();
}

// 2. Check in gate_visitors directly
$stmtPass = $conn->prepare("
    SELECT gv.visitor_id, gv.visitor_name, gv.visitor_phone, f.building_block, f.flat_number 
    FROM gate_visitors gv
    LEFT JOIN flats f ON gv.flat_id = f.flat_id
    WHERE gv.gate_pass_otp = ? AND gv.is_used = 0
    LIMIT 1
");
$stmtPass->bind_param("s", $cleanOtp);
$stmtPass->execute();
$passRes = $stmtPass->get_result();

if ($pRow = $passRes->fetch_assoc()) {
    $conn->query("UPDATE gate_visitors SET is_used = 1, entry_time = NOW() WHERE visitor_id = " . (int)$pRow['visitor_id']);
    echo json_encode([
        'success'      => true,
        'visitor_id'   => $pRow['visitor_id'],
        'visitor_name' => $pRow['visitor_name'],
        'phone'        => $pRow['visitor_phone'] ?? 'Verified Mobile',
        'unit'         => "Unit " . ($pRow['building_block'] ?? 'A') . "-" . ($pRow['flat_number'] ?? 'Common'),
        'time'         => date('Y-m-d H:i:s')
    ]);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid or already used OTP code!']);