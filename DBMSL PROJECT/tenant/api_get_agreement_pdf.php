<?php
// ============================================================================
// ARCHITECTURE: Printable Tenancy Agreement PDF View
// FILE: tenant/api_get_agreement_pdf.php
// ============================================================================
session_start();
require_once __DIR__ . '/../config/DBconnect.php';

$userId = $_SESSION['user_id'] ?? 3;

$stmt = $conn->prepare("SELECT ta.*, f.flat_number, f.building_block, f.square_feet, 
                               u.full_name AS owner_name, u.phone_number AS owner_phone, 
                               t.full_name AS tenant_name, t.phone_number AS tenant_phone, t.email AS tenant_email
                        FROM tenancy_agreements ta
                        JOIN flats f ON ta.flat_id = f.flat_id
                        JOIN users u ON ta.owner_id = u.user_id
                        JOIN users t ON ta.tenant_id = t.user_id
                        WHERE ta.tenant_id = ? AND ta.agreement_status = 'ACTIVE' LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$agr = $stmt->get_result()->fetch_assoc();

if (!$agr) {
    die("Active tenancy lease agreement not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tenancy Agreement — Unit <?= htmlspecialchars($agr['flat_number']) ?></title>
  <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8fafc; color: #0f172a; padding: 40px; margin: 0; }
    .deed-card { max-width: 720px; margin: 0 auto; background: #fff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 44px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .header { text-align: center; border-bottom: 2px solid #10b981; padding-bottom: 16px; margin-bottom: 24px; }
    .title { font-size: 20px; font-weight: 800; color: #10b981; letter-spacing: 0.5px; }
    .subtitle { font-size: 12.5px; color: #64748b; margin-top: 4px; }
    .clause { font-size: 13.5px; line-height: 1.7; margin-bottom: 18px; color: #334155; }
    .grid-party { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; background: #f1f5f9; padding: 18px; border-radius: 8px; margin-bottom: 24px; font-size: 13px; }
    .badge { background: #ecfdf5; color: #059669; padding: 4px 10px; border-radius: 4px; font-weight: 800; font-size: 11px; display: inline-block; margin-bottom: 10px; }
    .sign-row { display: flex; justify-content: space-between; margin-top: 50px; padding-top: 20px; border-top: 1px dashed #cbd5e1; font-size: 12.5px; text-align: center; }
    .sign-box { width: 180px; border-top: 1px solid #0f172a; padding-top: 6px; color: #64748b; font-weight: 700; }
    .actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 30px; }
    .btn { padding: 10px 18px; border-radius: 6px; font-weight: bold; cursor: pointer; border: none; font-size: 13px; }
    .btn-print { background: #10b981; color: #fff; }
    @media print {
      body { background: #fff; padding: 0; }
      .deed-card { border: none; box-shadow: none; padding: 0; }
      .actions { display: none; }
    }
  </style>
</head>
<body>
  <div class="deed-card">
    <div class="header">
      <span class="badge">REGISTERED LEASE DEED</span>
      <div class="title">GREENVIEW HEIGHTS RESIDENTIAL SOCIETY</div>
      <div class="subtitle">Official Tenancy Agreement &bull; Ref: GH-AGR-2026-0<?= htmlspecialchars($agr['agreement_id']) ?></div>
    </div>

    <div class="grid-party">
      <div>
        <strong>FIRST PARTY (LANDLORD):</strong>
        <div style="margin-top: 4px;"><?= htmlspecialchars($agr['owner_name']) ?></div>
        <div style="color: #64748b;">Phone: <?= htmlspecialchars($agr['owner_phone']) ?></div>
      </div>
      <div>
        <strong>SECOND PARTY (TENANT):</strong>
        <div style="margin-top: 4px;"><?= htmlspecialchars($agr['tenant_name']) ?></div>
        <div style="color: #64748b;">Phone: <?= htmlspecialchars($agr['tenant_phone']) ?></div>
      </div>
    </div>

    <div class="clause">
      <strong>1. Property Allocation:</strong> The Landlord hereby leases and demises unto the Tenant, and the Tenant takes representation of residential apartment <strong>Unit <?= htmlspecialchars($agr['flat_number']) ?> (Block <?= htmlspecialchars($agr['building_block']) ?>)</strong>, spanning an area of <strong><?= htmlspecialchars($agr['square_feet']) ?> Square Feet</strong> within Greenview Heights, Chittagong.
    </div>

    <div class="clause">
      <strong>2. Tenure Period:</strong> This agreement shall remain effective for a fixed duration of 12 months, commencing formally on <strong><?= htmlspecialchars($agr['lease_start']) ?></strong> and concluding on <strong><?= htmlspecialchars($agr['lease_end']) ?></strong>, subject to standard renewal bylaws.
    </div>

    <div class="clause">
      <strong>3. Financial Obligations:</strong> The agreed monthly rental consideration is fixed at <strong>৳ <?= number_format($agr['monthly_rent'], 2) ?></strong>, payable on or before the 5th day of every calendar month. Furthermore, a refundable security escrow deposit amounting to <strong>৳ <?= number_format($agr['security_deposit'], 2) ?></strong> has been deposited.
    </div>

    <div class="clause">
      <strong>4. Statutory Compliance:</strong> The Tenant agrees to strictly observe all security protocols, perimeter gate pass policies, and timely settlement of monthly split utility assessments.
    </div>

    <div class="sign-row">
      <div class="sign-box">Landlord Signature</div>
      <div class="sign-box">Committee Seal</div>
      <div class="sign-box">Resident Tenant Signature</div>
    </div>

    <div class="actions">
      <button class="btn btn-print" onclick="window.print()">Print / Save as PDF</button>
    </div>
  </div>

  <script>
    window.onload = function() {
      // Auto trigger browser print / PDF download modal
      window.print();
    };
  </script>
</body>
</html>