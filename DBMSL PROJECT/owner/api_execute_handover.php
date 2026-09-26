<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$ownerId = $_SESSION['user_id'] ?? 2;
$unit = trim($_POST['unit'] ?? '');
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$type = trim($_POST['type'] ?? 'TENANT');

$flatNum = str_replace(['Unit ', ' '], '', explode('(', $unit)[0]);
$flat = $conn->query("SELECT flat_id, rent_price, sale_price FROM flats WHERE flat_number = '{$flatNum}' LIMIT 1")->fetch_assoc();

if (!$flat) {
    echo json_encode(["success" => false, "message" => "Target flat not found."]);
    exit;
}
$flatId = $flat['flat_id'];


$userQuery = $conn->prepare("SELECT user_id FROM users WHERE phone_number = ? LIMIT 1");
$userQuery->bind_param("s", $phone);
$userQuery->execute();
$candidate = $userQuery->get_result()->fetch_assoc();
$candidateId = $candidate ? $candidate['user_id'] : 3;

if ($type === 'TENANT') {

    $startDate = date('Y-m-d');
    $endDate = date('Y-m-d', strtotime('+1 year'));
    $rent = $flat['rent_price'] > 0 ? $flat['rent_price'] : 38000;
    $deposit = $rent * 2;

    $ins = $conn->prepare("INSERT INTO tenancy_agreements (flat_id, tenant_id, owner_id, lease_start, lease_end, monthly_rent, security_deposit, agreement_status) VALUES (?, ?, ?, ?, ?, ?, ?, 'ACTIVE')");
    $ins->bind_param("iiissdd", $flatId, $candidateId, $ownerId, $startDate, $endDate, $rent, $deposit);
    $ins->execute();

    $conn->query("UPDATE flats SET flat_status = 'OCCUPIED', is_for_rent = 0 WHERE flat_id = {$flatId}");
    echo json_encode(["success" => true, "message" => "Tenancy agreement registered and keys handed over."]);
} else {

    $conn->query("UPDATE flats SET owner_id = {$candidateId}, flat_status = 'OCCUPIED', is_for_sale = 0 WHERE flat_id = {$flatId}");
    echo json_encode(["success" => true, "message" => "Title deed transferred to {$name}."]);
}