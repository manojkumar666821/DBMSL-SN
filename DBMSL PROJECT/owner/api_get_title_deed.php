<?php
session_start();
require_once __DIR__ . '/../config/DBconnect.php';

$userId = $_SESSION['user_id'] ?? 2;
$flatRef = trim($_GET['flat'] ?? 'A-401');

$stmt = $conn->prepare("SELECT f.*, u.full_name, u.phone_number, u.email 
                        FROM flats f 
                        JOIN users u ON f.owner_id = u.user_id 
                        WHERE u.user_id = ? AND (f.flat_number = ? OR ? = '') LIMIT 1");
$stmt->bind_param("iss", $userId, $flatRef, $flatRef);
$stmt->execute();
$flat = $stmt->get_result()->fetch_assoc();

if (!$flat) {
    die("Property deed record not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sub-Registry Title Deed — Unit <?= htmlspecialchars($flat['flat_number']) ?></title>
  <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8fafc; color: #0f172a; padding: 40px; margin: 0; }
    .deed-card { max-width: 720px; margin: 0 auto; background: #fff; border: 2px solid #2563eb; border-radius: 12px; padding: 44px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 16px; margin-bottom: 24px; }
    .title { font-size: 20px; font-weight: 800; color: #2563eb; }
    .badge { background: #eff6ff; color: #1d4ed8; padding: 4px 10px; border-radius: 4px; font-weight: 800; font-size: 11px; display: inline-block; margin-bottom: 10px; }
    .clause { font-size: 13.5px; line-height: 1.7; margin-bottom: 18px; color: #334155; }
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; background: #f1f5f9; padding: 16px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; }
    .sign-row { display: flex; justify-content: space-between; margin-top: 50px; font-size: 12.5px; text-align: center; color: #64748b; font-weight: 700; }
    .sign-box { width: 180px; border-top: 1px solid #0f172a; padding-top: 6px; }
    @media print { body { background: #fff; padding: 0; } .deed-card { border: none; box-shadow: none; } .btn-print { display: none; } }
  </style>
</head>
<body>
  <div class="deed-card">
    <div class="header">
      <span class="badge">SUB-REGISTRY & MUTATION DEED</span>
      <div class="title">GREENVIEW HEIGHTS APARTMENT SUITE</div>
      <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Deed Ref: REG-CHIT-<?= htmlspecialchars($flat['flat_id']) ?>/2026</div>
    </div>

    <div class="grid">
      <div>
        <strong>Registered Owner:</strong>
        <div><?= htmlspecialchars($flat['full_name']) ?></div>
        <div style="color:#64748b;">Phone: <?= htmlspecialchars($flat['phone_number']) ?></div>
      </div>
      <div>
        <strong>Demised Premise:</strong>
        <div>Unit <?= htmlspecialchars($flat['flat_number']) ?> (Block <?= htmlspecialchars($flat['building_block']) ?>)</div>
        <div style="color:#64748b;">Floor Area: <?= htmlspecialchars($flat['square_feet']) ?> SqFt</div>
      </div>
    </div>

    <div class="clause">
      <strong>Certification of Ownership:</strong> This official document attests that absolute ownership and perpetual mutation rights of residential apartment <strong>Unit <?= htmlspecialchars($flat['flat_number']) ?></strong> have been duly cleared, verified, and recorded in the permanent Society Asset Registry under Nasirabad Sub-Registry Jurisdiction, Chittagong.
    </div>

    <div class="clause">
      <strong>Encumbrance Clearance:</strong> The property is verified free from statutory mortgages, liens, or unallocated dues. The owner holds undisputed authority to inhabit, lease, or permanently transfer title deed privileges.
    </div>

    <div class="sign-row">
      <div class="sign-box">Registered Flat Owner</div>
      <div class="sign-box">Society Registrar Seal</div>
      <div class="sign-box">Sub-Registry Officer</div>
    </div>

    <div style="text-align: right; margin-top: 30px;">
      <button class="btn-print" onclick="window.print()" style="padding: 10px 18px; background: #2563eb; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">Print / Save Deed PDF</button>
    </div>
  </div>
  <script>window.onload = function() { window.print(); };</script>
</body>
</html>