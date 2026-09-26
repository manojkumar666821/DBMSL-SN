<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$userId = $_SESSION['user_id'] ?? 3;
$method = trim($_POST['pay_method'] ?? 'bKash Online Gateway');
$trxId = trim($_POST['trx_id'] ?? '');

if (empty($trxId)) {
    echo json_encode(["success" => false, "message" => "Transaction ID is required"]);
    exit;
}

$stmt = $conn->prepare("SELECT invoice_id, total_amount FROM invoices WHERE tenant_id = ? AND status = 'UNPAID' ORDER BY invoice_id DESC LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$invoice = $stmt->get_result()->fetch_assoc();

if (!$invoice) {
    echo json_encode(["success" => false, "message" => "No pending invoice found"]);
    exit;
}

$invoiceId = $invoice['invoice_id'];
$amount = $invoice['total_amount'];

$pay = $conn->prepare("INSERT INTO payments (invoice_id, amount_paid, payment_method, transaction_reference, payment_date) VALUES (?, ?, ?, ?, NOW())");
$pay->bind_param("idss", $invoiceId, $amount, $method, $trxId);

if ($pay->execute()) {
    $upd = $conn->prepare("UPDATE invoices SET status = 'PAID' WHERE invoice_id = ?");
    $upd->bind_param("i", $invoiceId);
    $upd->execute();

    echo json_encode([
        "success" => true,
        "message" => "Payment successful",
        "data" => ["invoice_ref" => "INV-" . $invoiceId, "amount" => $amount, "method" => $method, "trx_id" => $trxId]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Payment insert failed"]);
}