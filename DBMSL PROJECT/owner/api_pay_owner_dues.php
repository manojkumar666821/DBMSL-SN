<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 2;
$method = trim($_POST['pay_method'] ?? 'bKash Online Gateway');
$trxId = trim($_POST['trx_id'] ?? '');
$amount = (float)($_POST['amount'] ?? 12000.00);

if (empty($trxId)) {
    echo json_encode(["success" => false, "message" => "TrxID is required."]);
    exit;
}

// ওনারের আনপেইড ইনভয়েস খুঁজে নেওয়া
$invQuery = $conn->query("SELECT invoice_id FROM invoices WHERE status = 'UNPAID' ORDER BY invoice_id DESC LIMIT 1");
$inv = $invQuery->fetch_assoc();

if ($inv) {
    $invoiceId = (int)$inv['invoice_id'];
    $conn->query("UPDATE invoices SET status = 'PAID' WHERE invoice_id = {$invoiceId}");
} else {
    // আনপেইড না থাকলে নতুন পেইড ইনভয়েস তৈরি
    $ins = $conn->prepare("INSERT INTO invoices (agreement_id, flat_id, tenant_id, invoice_type, billing_period, rent_portion, society_portion, utility_portion, total_amount, due_date, status, created_at) VALUES (1, 1, ?, 'MAINTENANCE', 'September 2026', 0.00, 9500.00, 2500.00, ?, '2026-09-30', 'PAID', NOW())");
    $ins->bind_param("id", $userId, $amount);
    $ins->execute();
    $invoiceId = $ins->insert_id;
}

// payments টেবিলে সরাসরি ট্রানজেকশন রেকর্ড ইনসার্ট
$pay = $conn->prepare("INSERT INTO payments (invoice_id, amount_paid, payment_method, transaction_reference, payment_date) VALUES (?, ?, ?, ?, NOW())");
$pay->bind_param("idss", $invoiceId, $amount, $method, $trxId);

if ($pay->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Payment successfully recorded in database! Invoice settled.",
        "invoice_id" => $invoiceId,
        "payment_id" => $pay->insert_id
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Payment insert failed: " . $conn->error]);
}