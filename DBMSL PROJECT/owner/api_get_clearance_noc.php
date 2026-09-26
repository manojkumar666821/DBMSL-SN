<?php
session_start();
require_once __DIR__ . '/../config/DBconnect.php';

$userId = $_SESSION['user_id'] ?? 2;
$owner = $conn->query("SELECT full_name, phone_number FROM users WHERE user_id = {$userId}")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>No-Dues Clearance Certificate (NOC)</title>
  <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8fafc; color: #0f172a; padding: 40px; margin: 0; }
    .noc-card { max-width: 720px; margin: 0 auto; background: #fff; border: 2px solid #059669; border-radius: 12px; padding: 48px; position: relative; }
    .header { text-align: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 28px; }
    .seal { position: absolute; right: 48px; bottom: 90px; border: 3px double #059669; color: #059669; font-weight: 800; padding: 12px 18px; border-radius: 8px; text-transform: uppercase; font-size: 12px; }
    .sign-row { display: flex; justify-content: space-between; margin-top: 60px; font-size: 12px; color: #64748b; font-weight: 700; }
    @media print { body { background:#fff; padding:0; } .noc-card { border: none; } .btn-print { display:none; } }
  </style>
</head>
<body>
  <div class="noc-card">
    <div class="header">
      <div style="font-size: 11px; font-weight: 800; color: #059669; text-transform: uppercase; letter-spacing: 1px;">Official Executive Clearance</div>
      <h2 style="margin: 6px 0; color: #0f172a;">GREENVIEW HEIGHTS APARTMENT SOCIETY</h2>
      <div style="font-size: 13px; color: #64748b;">Nasirabad, Chittagong &bull; Ref: GH-NOC-2026-<?= $userId ?></div>
    </div>

    <h3 style="text-align: center; text-decoration: underline; margin-bottom: 24px;">NO-OBJECTION / DUES CLEARANCE CERTIFICATE</h3>

    <p style="font-size: 14px; line-height: 1.8; color: #334155;">
      This is to formally certify that all monthly society maintenance levies, common area electricity surcharges, backup generator provisions, and dedicated water booster assessments pertaining to owned properties under <strong><?= htmlspecialchars($owner['full_name'] ?? 'Zubaer Hossain, FCA') ?></strong> (Contact: <?= htmlspecialchars($owner['phone_number'] ?? '+880 1711-223344') ?>) have been <strong>fully settled and reconciled up to September 2026</strong>.
    </p>

    <p style="font-size: 14px; line-height: 1.8; color: #334155;">
      The Greenview Heights Executive Committee and Asset Management Desk confirm that there are <strong>nil outstanding liabilities</strong> standing against the registered units as of today, <?= date('d F Y') ?>.
    </p>

    <div class="seal">Official Seal<br>Verified Clear</div>

    <div class="sign-row">
      <div>Estate Superintendent</div>
      <div>Honorary Treasurer</div>
      <div>President / Secretary</div>
    </div>

    <div style="margin-top: 30px; text-align: right;">
      <button class="btn-print" onclick="window.print()" style="padding:10px 18px; background:#059669; color:#fff; border:none; border-radius:6px; cursor:pointer; font-weight:bold;">Print / Save as PDF</button>
    </div>
  </div>
  <script>window.onload = function() { window.print(); };</script>
</body>
</html>