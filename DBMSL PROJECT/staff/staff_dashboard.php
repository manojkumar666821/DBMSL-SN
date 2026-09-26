<?php
// ============================================================================
// ARCHITECTURE: Enterprise Security Guard & On-Ground Staff Portal (Single-File)
// FILE: staff/staff_dashboard.php (or index.php)
// REFACTORED: Dynamic Button State Transitions (Check-out -> Exited, Repair -> Resolved)
//             + Auto Counter Sync + Emergency Speed-Dial Removed + Visible Initials
// ============================================================================
session_start();
date_default_timezone_set('Asia/Dhaka');

$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

$fullName = $isLoggedIn ? ($_SESSION['full_name'] ?? 'Md. Al-Amin Hossain') : 'Md. Al-Amin Hossain';
$nameParts = preg_split('/\s+/', trim($fullName));
$avatarInitials = count($nameParts) > 1 
    ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1))
    : strtoupper(substr($fullName, 0, 2));

$sessionUser = [
    'is_logged_in' => $isLoggedIn,
    'user_id'      => $isLoggedIn ? (int)$_SESSION['user_id'] : 5,
    'first_name'   => $nameParts[0] ?? 'Al-Amin',
    'last_name'    => end($nameParts) ?? 'Hossain',
    'full_name'    => $fullName,
    'initials'     => !empty($avatarInitials) ? $avatarInitials : 'AH',
    'email'        => $isLoggedIn ? ($_SESSION['email'] ?? 'gate1.command@greenview.internal') : 'gate1.command@greenview.internal',
    'phone'        => '+880 1819-001122',
    'role_id'      => 5,
    'role_name'    => 'STAFF',
    'duty_post'    => 'Main Perimeter Gate 01 & Control Room',
    'shift'        => 'Day Guard Shift (08:00 AM – 08:00 PM)'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Greenview Heights — Gate Perimeter & Staff Operations Command</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>

  <style>
    :root {
      --bg-canvas: #f8fafc;
      --bg-surface: #ffffff;
      --bg-surface-alt: #f1f5f9;
      --bg-glass: rgba(255, 255, 255, 0.94);
      --border-subtle: #e2e8f0;
      --border-strong: #cbd5e1;

      --text-main: #090d16;
      --text-secondary: #475569;
      --text-muted: #64748b;

      --primary-accent: #2563eb;
      --primary-accent-hover: #1d4ed8;
      --primary-accent-soft: #eff6ff;

      --emerald: #10b981;
      --emerald-hover: #059669;
      --emerald-soft: #ecfdf5;
      --amber: #f59e0b;
      --amber-soft: #fffbeb;
      --rose: #ef4444;
      --rose-soft: #fef2f2;
      --indigo: #6366f1;
      --indigo-soft: #eef2ff;

      --radius-xs: 8px;
      --radius-sm: 12px;
      --radius-md: 16px;
      --radius-lg: 24px;
      --radius-full: 9999px;

      --shadow-sm: 0 2px 4px rgba(15, 23, 42, 0.05);
      --shadow-md: 0 10px 25px -5px rgba(15, 23, 42, 0.06);
      --shadow-lg: 0 20px 35px -8px rgba(15, 23, 42, 0.12);

      --font-sans: 'Plus Jakarta Sans', sans-serif;
      --font-mono: 'JetBrains Mono', monospace;
      --transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    }

    [data-theme="dark"] {
      --bg-canvas: #090d16 !important;
      --bg-surface: #111827 !important;
      --bg-surface-alt: #1a2234 !important;
      --bg-glass: rgba(17, 24, 39, 0.94) !important;
      --border-subtle: #1f2937 !important;
      --border-strong: #374151 !important;

      --text-main: #f8fafc !important;
      --text-secondary: #cbd5e1 !important;
      --text-muted: #94a3b8 !important;

      --primary-accent: #3b82f6 !important;
      --primary-accent-hover: #60a5fa !important;
      --primary-accent-soft: rgba(59, 130, 246, 0.15) !important;
      --emerald-soft: rgba(16, 185, 129, 0.15) !important;
      --amber-soft: rgba(245, 158, 11, 0.15) !important;
      --rose-soft: rgba(239, 68, 68, 0.15) !important;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background-color: var(--bg-canvas);
      color: var(--text-main);
      font-family: var(--font-sans);
      line-height: 1.6;
      font-size: 14px;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
      transition: background-color 0.25s ease, color 0.25s ease;
    }

    * { scrollbar-width: none !important; }
    *::-webkit-scrollbar { display: none !important; width: 0 !important; height: 0 !important; }

    .container {
      max-width: 1340px;
      margin: 0 auto;
      padding: 0 clamp(16px, 3vw, 28px);
      width: 100%;
    }

    /* TOP APPLICATION NAVIGATION */
    .app-nav {
      position: sticky;
      top: 0;
      z-index: 500;
      background: var(--bg-glass);
      backdrop-filter: blur(14px);
      border-bottom: 1px solid var(--border-subtle);
    }

    .nav-container {
      height: 74px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
    }

    .brand-cluster {
      display: flex;
      align-items: center;
      gap: 12px;
      cursor: pointer;
      user-select: none;
      flex-shrink: 0;
    }

    .brand-symbol {
      width: 42px;
      height: 42px;
      border-radius: var(--radius-sm);
      background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 12px rgba(14, 165, 233, 0.35);
    }

    .brand-title {
      font-size: 17px;
      font-weight: 800;
      letter-spacing: -0.03em;
      white-space: nowrap;
    }

    .nav-tabs-group {
      display: flex;
      align-items: center;
      gap: 4px;
      list-style: none;
      background: var(--bg-surface-alt);
      padding: 4px 6px;
      border-radius: var(--radius-full);
      border: 1px solid var(--border-subtle);
      overflow-x: auto;
      max-width: 100%;
      flex-shrink: 1;
    }

    .nav-tab-link {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 7px 15px;
      border-radius: var(--radius-full);
      font-size: 13px;
      font-weight: 700;
      color: var(--text-muted);
      border: none;
      background: transparent;
      cursor: pointer;
      white-space: nowrap;
      transition: var(--transition);
      position: relative;
    }

    .nav-tab-link:hover { color: var(--text-main); }
    .nav-tab-link.active {
      background: var(--bg-surface);
      color: #0284c7;
      box-shadow: var(--shadow-sm);
    }

    .nav-actions-cluster {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-shrink: 0;
      position: relative;
    }

    /* NOTIFICATION SYSTEM */
    .notif-bell-btn {
      width: 40px;
      height: 40px;
      border-radius: var(--radius-full);
      background: var(--bg-surface-alt);
      border: 1px solid var(--border-subtle);
      color: var(--text-main);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      position: relative;
      transition: var(--transition);
    }
    .notif-dot {
      position: absolute;
      top: 9px;
      right: 9px;
      width: 8px;
      height: 8px;
      background: var(--rose);
      border-radius: var(--radius-full);
      border: 1.5px solid var(--bg-surface);
    }

    .notif-flyout-card {
      position: absolute;
      top: calc(100% + 14px);
      right: 50px;
      width: 330px;
      background: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-md);
      box-shadow: var(--shadow-lg);
      padding: 16px;
      display: none;
      flex-direction: column;
      gap: 10px;
      z-index: 600;
    }
    .notif-flyout-card.active { display: flex; animation: flyoutFade 0.2s cubic-bezier(0.16, 1, 0.3, 1); }

    .notif-item {
      padding: 10px 12px;
      border-radius: 8px;
      font-size: 12.5px;
      line-height: 1.4;
      display: flex;
      flex-direction: column;
      gap: 3px;
    }
    .notif-item.emergency { background: var(--rose-soft); border-left: 3px solid var(--rose); }
    .notif-item.repair { background: var(--amber-soft); border-left: 3px solid var(--amber); }
    .notif-item.order { background: var(--primary-accent-soft); border-left: 3px solid var(--primary-accent); }

    .theme-trigger-btn {
      width: 40px;
      height: 40px;
      border-radius: var(--radius-full);
      background: var(--bg-surface-alt);
      border: 1px solid var(--border-subtle);
      color: var(--text-main);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: var(--transition);
    }

    /* GUARANTEED VISIBLE INITIALS (AH) */
    .profile-circle-btn {
      width: 42px;
      height: 42px;
      border-radius: var(--radius-full);
      background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
      color: #ffffff !important;
      font-size: 15px !important;
      font-weight: 800 !important;
      letter-spacing: 0.5px;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      border: 2px solid rgba(255, 255, 255, 0.9) !important;
      cursor: pointer;
      box-shadow: var(--shadow-sm);
      transition: var(--transition);
      user-select: none;
      line-height: 1 !important;
      text-shadow: 0 1px 3px rgba(0,0,0,0.4);
    }
    .profile-circle-btn:hover {
      transform: scale(1.06);
      box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.25);
    }

    /* VIEW PANES */
    .view-pane {
      display: none;
      padding: 32px 0 80px;
      animation: fadeInView 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .view-pane.active { display: block; }

    @keyframes fadeInView {
      from { opacity: 0; transform: translateY(8px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes flyoutFade {
      from { opacity: 0; transform: translateY(-8px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* QUICK OTP STRIP */
    .quick-otp-strip {
      background: var(--bg-surface);
      border: 1.5px solid var(--border-subtle);
      border-radius: var(--radius-lg);
      padding: 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
      margin-bottom: 30px;
      box-shadow: var(--shadow-md);
      flex-wrap: wrap;
    }
    .otp-input-field {
      font-family: var(--font-mono);
      font-size: 22px;
      font-weight: 800;
      letter-spacing: 4px;
      padding: 12px 20px;
      border-radius: var(--radius-sm);
      border: 2px solid var(--border-strong);
      background: var(--bg-surface-alt);
      outline: none;
      max-width: 260px;
      text-align: center;
      color: var(--text-main);
      transition: var(--transition);
    }
    .otp-input-field:focus {
      border-color: #0284c7;
      background: var(--bg-surface);
      box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.2);
    }

    .hero-telemetry-cluster {
      display: grid;
      grid-template-columns: repeat(2, minmax(140px, 1fr));
      gap: 16px;
      margin-top: 24px;
      max-width: 440px;
    }

    .telemetry-card {
      background: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-md);
      padding: 16px;
      box-shadow: var(--shadow-sm);
    }

    .telemetry-val {
      font-size: 20px;
      font-weight: 800;
      display: block;
      color: var(--text-main);
    }

    .telemetry-lbl {
      font-size: 11px;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
    }

    .spec-sheet-panel {
      background: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-lg);
      padding: clamp(18px, 3vw, 32px);
      box-shadow: var(--shadow-sm);
      margin-bottom: 28px;
    }

    .panel-header-cluster {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      flex-wrap: wrap;
      gap: 14px;
    }

    /* RESPONSIVE TABLE TO CARD SYSTEM */
    .table-responsive-box {
      width: 100%;
      overflow-x: auto;
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-sm);
      -webkit-overflow-scrolling: touch;
    }

    .standard-table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
      font-size: 13.5px;
    }

    .standard-table th {
      background: var(--bg-surface-alt);
      color: var(--text-muted);
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      padding: 14px 16px;
      border-bottom: 1px solid var(--border-subtle);
      white-space: nowrap;
    }

    .standard-table td {
      padding: 15px 16px;
      border-bottom: 1px solid var(--border-subtle);
      color: var(--text-main);
    }
    .standard-table tr:last-child td { border-bottom: none; }

    @media (max-width: 768px) {
      .table-responsive-box {
        border: none;
        overflow-x: visible;
      }
      .standard-table { min-width: 100% !important; }
      .standard-table thead { display: none; }
      .standard-table, .standard-table tbody, .standard-table tr, .standard-table td {
        display: block;
        width: 100%;
      }
      .standard-table tr {
        background: var(--bg-surface);
        border: 1.5px solid var(--border-subtle);
        border-radius: var(--radius-md);
        margin-bottom: 14px;
        padding: 12px 16px;
        box-shadow: var(--shadow-sm);
      }
      .standard-table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px dashed var(--border-subtle);
        text-align: right;
        font-size: 13px;
      }
      .standard-table td:last-child {
        border-bottom: none;
        padding-top: 10px;
        justify-content: flex-end;
      }
      .standard-table td::before {
        content: attr(data-label);
        font-weight: 800;
        font-size: 11px;
        text-transform: uppercase;
        color: var(--text-muted);
        text-align: left;
        margin-right: 12px;
      }
    }

    .badge-pill {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 4px 10px;
      border-radius: var(--radius-full);
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      white-space: nowrap;
    }
    .badge-pill.active, .badge-pill.checked-in { background: var(--emerald-soft); color: var(--emerald); }
    .badge-pill.pending, .badge-pill.progress { background: var(--amber-soft); color: var(--amber); }
    .badge-pill.checked-out, .badge-pill.urgent { background: var(--rose-soft); color: var(--rose); }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 10px 18px;
      border-radius: var(--radius-xs);
      font-family: inherit;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      text-decoration: none;
      border: 1px solid transparent;
      transition: var(--transition);
      white-space: nowrap;
    }
    .btn-sky { background: #0284c7; color: #fff; }
    .btn-sky:hover { background: #0369a1; }
    .btn-emerald { background: var(--emerald); color: #fff; }
    .btn-emerald:hover { background: var(--emerald-hover); }
    .btn-outline { background: transparent; border-color: var(--border-subtle); color: var(--text-main); }
    .btn-outline:hover { background: var(--bg-surface-alt); }
    .btn-danger { background: var(--rose); color: #fff; }
    .btn-sm { padding: 6px 12px; font-size: 12px; }

    .filter-ctrl { display: flex; flex-direction: column; gap: 6px; }
    .filter-ctrl label { font-size: 11px; font-weight: 800; text-transform: uppercase; color: #475569; letter-spacing: 0.03em; }
    [data-theme="dark"] .filter-ctrl label { color: #94a3b8; }

    .input-box {
      background: #f1f5f9;
      border: 1.5px solid #cbd5e1;
      border-radius: 8px;
      padding: 11px 14px;
      font-family: inherit;
      font-size: 13.5px;
      color: #090d16;
      outline: none;
      width: 100%;
      transition: var(--transition);
    }
    .input-box:focus {
      border-color: #0284c7 !important;
      background: #ffffff !important;
      box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.2);
    }
    [data-theme="dark"] .input-box {
      background: #1a2234;
      border-color: #374151;
      color: #f8fafc;
    }

    /* MODAL */
    .modal-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.60);
      backdrop-filter: blur(5px);
      display: none;
      align-items: center;
      justify-content: center;
      padding: 20px;
      z-index: 1000;
    }
    .modal-backdrop.active { display: flex !important; }

    .modal-card {
      background: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      border-radius: 20px;
      max-width: 480px;
      width: 100%;
      box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
      overflow: hidden;
      animation: popInModal 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes popInModal {
      from { transform: scale(0.96); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }

    .modal-header-pic2 {
      padding: 22px 24px 14px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .modal-header-pic2 h3 {
      font-size: 16.5px;
      font-weight: 800;
      color: var(--text-main);
    }
    .modal-close-btn {
      background: none;
      border: none;
      color: var(--text-muted);
      cursor: pointer;
      padding: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: var(--radius-full);
      transition: var(--transition);
    }
    .modal-close-btn:hover { background: var(--bg-surface-alt); color: var(--text-main); }

    .modal-body-pic2 {
      padding: 10px 24px 24px;
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .modal-actions-pic2 {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      gap: 12px;
      margin-top: 10px;
    }
    .btn-pic2-cancel {
      background: #ffffff;
      border: 1.5px solid #e2e8f0;
      color: #334155;
      font-weight: 700;
      font-size: 13.5px;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      transition: var(--transition);
    }
    .btn-pic2-cancel:hover { background: #f8fafc; }
    [data-theme="dark"] .btn-pic2-cancel {
      background: #1e293b;
      border-color: #334155;
      color: #cbd5e1;
    }

    .btn-pic2-submit {
      background: #0284c7;
      border: 1px solid #0284c7;
      color: #ffffff;
      font-weight: 700;
      font-size: 13.5px;
      padding: 10px 22px;
      border-radius: 8px;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(14, 165, 233, 0.28);
      transition: var(--transition);
    }
    .btn-pic2-submit:hover { background: #0369a1; }

    .toast-pill {
      position: fixed;
      bottom: 24px;
      right: 24px;
      background: #0f172a;
      color: #fff;
      padding: 12px 20px;
      border-radius: var(--radius-sm);
      font-size: 13px;
      font-weight: 600;
      display: none;
      align-items: center;
      gap: 10px;
      box-shadow: var(--shadow-lg);
      z-index: 2000;
    }
    .toast-pill.active { display: flex; }

    /* CLEAN 3-ITEM MOBILE BOTTOM DOCK */
    .mobile-bottom-bar {
      display: none;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      height: 64px;
      background: var(--bg-glass);
      backdrop-filter: blur(14px);
      border-top: 1px solid var(--border-subtle);
      z-index: 400;
      justify-content: space-around;
      align-items: center;
      padding: 0 10px;
    }

    .mobile-dock-btn {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 3px;
      background: none;
      border: none;
      color: var(--text-muted);
      font-size: 10px;
      font-weight: 700;
      cursor: pointer;
      flex: 1;
      padding: 6px 0;
    }
    .mobile-dock-btn.active { color: #0284c7; }

    .app-footer {
      background: var(--bg-surface);
      border-top: 1px solid var(--border-subtle);
      padding: 50px 0 24px;
      margin-top: auto;
    }
    .footer-grid {
      display: grid;
      grid-template-columns: 1.4fr 0.9fr 1.1fr;
      gap: 40px;
      margin-bottom: 30px;
    }

    @media (max-width: 992px) {
      .hero-split { grid-template-columns: 1fr; }
      .nav-tabs-group { display: none; }
      .mobile-bottom-bar { display: flex; }
      body { padding-bottom: 70px; }
      .footer-grid { grid-template-columns: 1fr; gap: 24px; }
    }
    @media (max-width: 640px) {
      .hero-headline { font-size: 26px; }
      .nav-container { height: 68px; }
      .quick-otp-strip { flex-direction: column; align-items: stretch; }
      .otp-input-field { max-width: 100%; }
    }
  </style>
</head>
<body>

  <!-- TOP APPLICATION NAVIGATION DOCK -->
  <header class="app-nav">
    <div class="container nav-container">
      
      <div class="brand-cluster" onclick="routeTo('home')">
        <div class="brand-symbol">
          <i data-lucide="shield-alert"></i>
        </div>
        <div>
          <span class="brand-title">Greenview Heights</span>
          <div style="font-size: 10.5px; font-weight: 800; color: #0284c7; text-transform: uppercase;">Gate & Duty Staff Command</div>
        </div>
      </div>

      <!-- Center Navigation Tabs (EXACT 3 TABS, EMERGENCY REMOVED) -->
      <ul class="nav-tabs-group">
        <li>
          <button class="nav-tab-link active" id="tabNavHome" onclick="routeTo('home')">
            <i data-lucide="scan" style="width: 15px;"></i> Gate Clearance
          </button>
        </li>
        <li>
          <button class="nav-tab-link" id="tabNavRepairs" onclick="routeTo('repairs')">
            <i data-lucide="wrench" style="width: 15px;"></i> Repair Job Queue
            <span id="repairTabBadge" style="position: absolute; top: 4px; right: 6px; width: 7px; height: 7px; background: var(--amber); border-radius: var(--radius-full);"></span>
          </button>
        </li>
        <li>
          <button class="nav-tab-link" id="tabNavParking" onclick="routeTo('parking')">
            <i data-lucide="car" style="width: 15px;"></i> Parking & Vehicles
          </button>
        </li>
      </ul>

      <!-- Right Controls: Notification Bell + Theme Toggle + Profile Button -->
      <div class="nav-actions-cluster">
        
        <!-- STAFF NOTIFICATION BELL WITH ALERTS -->
        <button class="notif-bell-btn" id="notifBellBtn" title="Maintenance Tasks & Security Alerts" onclick="toggleNotificationDropdown()">
          <i data-lucide="bell" style="width: 18px;"></i>
          <span class="notif-dot" id="notifBadgeDot"></span>
        </button>

        <!-- NOTIFICATION FLYOUT -->
        <div class="notif-flyout-card" id="notifFlyout">
          <div style="font-weight: 800; font-size: 13.5px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px; display:flex; justify-content:space-between; align-items:center;">
            <span>Staff Duty Notifications</span>
            <span style="font-size:10.5px; color:#0284c7; font-weight:700;">3 Active</span>
          </div>
          <div style="display: flex; flex-direction: column; gap: 8px; font-size: 12px;">
            <div class="notif-item emergency">
              <strong style="color: var(--rose); display:flex; align-items:center; gap:5px;">
                <i data-lucide="alert-triangle" style="width:13px;"></i> Emergency Plumber Alert
              </strong>
              <div>Unit B-204 overhead pipe cracked. Resident requested immediate handyman entry.</div>
              <span style="font-size: 10px; color: var(--text-muted);">10 mins ago</span>
            </div>

            <div class="notif-item repair">
              <strong style="color: var(--amber); display:flex; align-items:center; gap:5px;">
                <i data-lucide="wrench" style="width:13px;"></i> New Repair Queued
              </strong>
              <div>Ticket #TKT-102: Balcony switch repair logged for Unit A-301.</div>
              <span style="font-size: 10px; color: var(--text-muted);">25 mins ago</span>
            </div>

            <div class="notif-item order">
              <strong style="color: var(--primary-accent); display:flex; align-items:center; gap:5px;">
                <i data-lucide="shield" style="width:13px;"></i> Gate Security Order
              </strong>
              <div>Vendor trucks restricted after 08:00 PM due to evening AGM gathering.</div>
              <span style="font-size: 10px; color: var(--text-muted);">1 hour ago</span>
            </div>
          </div>
        </div>

        <button class="theme-trigger-btn" id="themeBtn" title="Toggle Display Theme" onclick="toggleThemeMode()">
          <i data-lucide="moon" id="themeIcon"></i>
        </button>

        <!-- PROFILE CIRCLE BUTTON WITH CRISP VISIBLE INITIALS (AH) -->
        <button class="profile-circle-btn" id="profileCircleBtn" title="Duty Guard Profile" onclick="routeTo('profile')">
          <?= htmlspecialchars($sessionUser['initials'], ENT_QUOTES, 'UTF-8') ?>
        </button>
      </div>

    </div>
  </header>

  <!-- CLEAN 3-ITEM MOBILE DOCK (NO PROFILE TAB, NO EMERGENCY TAB) -->
  <nav class="mobile-bottom-bar">
    <button class="mobile-dock-btn active" id="mDockHome" onclick="routeTo('home')">
      <i data-lucide="scan" style="width: 18px;"></i>
      <span>Gate OTP</span>
    </button>
    <button class="mobile-dock-btn" id="mDockRepairs" onclick="routeTo('repairs')">
      <i data-lucide="wrench" style="width: 18px;"></i>
      <span>Repairs</span>
    </button>
    <button class="mobile-dock-btn" id="mDockParking" onclick="routeTo('parking')">
      <i data-lucide="car" style="width: 18px;"></i>
      <span>Parking</span>
    </button>
  </nav>

  <!-- ========================================================================
       VIEW 0: GATE CLEARANCE & LIVE OTP VERIFIER (#view-home)
       ======================================================================== -->
  <main class="view-pane active" id="view-home">
    <div class="container">
      <div class="quick-otp-strip">
        <div style="display:flex; align-items:center; gap:16px;">
          <div style="width:48px; height:48px; border-radius:12px; background:rgba(14,165,233,0.15); color:#0284c7; display:flex; align-items:center; justify-content:center;">
            <i data-lucide="key" style="width:24px; height:24px;"></i>
          </div>
          <div>
            <h2 style="font-size:18px; font-weight:800;">Perimeter Barrier OTP Verification</h2>
            <p style="font-size:12.5px; color:var(--text-muted);">Enter visitor or delivery rider's 6-digit clearance code to authorize barrier opening.</p>
          </div>
        </div>

        <form style="display:flex; gap:10px; align-items:center;" onsubmit="handleVerifyOtp(event)">
          <input type="text" id="inputVerifyOtp" class="otp-input-field" placeholder="###-###" maxlength="7" required>
          <button type="submit" class="btn btn-sky" style="padding: 13px 22px; font-size:14px;">
            <i data-lucide="shield-check"></i> Verify & Open Barrier
          </button>
        </form>
      </div>
    </div>

    <!-- Telemetry & Shift Metrics -->
    <div class="container">
      <div class="hero-telemetry-cluster" style="max-width:100%; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 28px;">
        <div class="telemetry-card">
          <span class="telemetry-val" style="color:#0284c7;">GATE 01</span>
          <span class="telemetry-lbl">Active Control Post</span>
        </div>
        <div class="telemetry-card">
          <span class="telemetry-val" id="statCheckedInCount">14 Ingress</span>
          <span class="telemetry-lbl">Visitors Inside Compound</span>
        </div>
        <div class="telemetry-card">
          <span class="telemetry-val" style="color:var(--amber);" id="statPendingJobsCount">2 Active</span>
          <span class="telemetry-lbl">Pending Repair Tickets</span>
        </div>
      </div>
    </div>

    <!-- Live Ingress Registry Table -->
    <div class="container">
      <div class="spec-sheet-panel">
        <div class="panel-header-cluster">
          <div>
            <h2><i data-lucide="users" style="color:#0284c7;"></i> Today's Live Ingress & Visitor Registry</h2>
            <p style="font-size:13px; color:var(--text-muted);">Real-time log of verified entries, visiting purposes, and checkout states.</p>
          </div>
          <button class="btn btn-outline" onclick="openModal('modalManualVisitor')">
            <i data-lucide="user-plus"></i> Manual Walk-in Entry
          </button>
        </div>

        <div class="table-responsive-box">
          <table class="standard-table">
            <thead>
              <tr>
                <th>Pass Ref</th>
                <th>Visitor Name</th>
                <th>Contact Phone</th>
                <th>Destination Unit</th>
                <th>Entry OTP</th>
                <th>Entry Timestamp</th>
                <th>Clearance Status</th>
                <th>Gate Action</th>
              </tr>
            </thead>
            <tbody id="tableBodyVisitors">
              <tr id="visRow1">
                <td data-label="Pass Ref"><strong>#VIS-001</strong></td>
                <td data-label="Visitor">Kamrul Ahsan</td>
                <td data-label="Phone"><code>01712345678</code></td>
                <td data-label="Unit">Unit A-401 (Block A)</td>
                <td data-label="OTP"><strong style="font-family:var(--font-mono); color:var(--primary-accent); letter-spacing:1px;">482-195</strong></td>
                <td data-label="Timestamp">2026-09-24 10:14:02</td>
                <td data-label="Status"><span class="badge-pill active" id="badgeVis1">CHECKED_IN</span></td>
                <td data-label="Action" id="actionVisCell1">
                  <button class="btn btn-danger btn-sm" onclick="markVisitorCheckOut('1')">
                    <i data-lucide="log-out" style="width:12px;"></i> Check-out
                  </button>
                </td>
              </tr>
              <tr id="visRow2">
                <td data-label="Pass Ref"><strong>#VIS-002</strong></td>
                <td data-label="Visitor">S. M. Mahfuzul Alam (Viewing)</td>
                <td data-label="Phone"><code>01719876543</code></td>
                <td data-label="Unit">Unit B-302 (Block B)</td>
                <td data-label="OTP"><strong style="font-family:var(--font-mono); color:var(--primary-accent); letter-spacing:1px;">731-904</strong></td>
                <td data-label="Timestamp">2026-09-24 09:14:22</td>
                <td data-label="Status"><span class="badge-pill active" id="badgeVis2">CHECKED_IN</span></td>
                <td data-label="Action" id="actionVisCell2">
                  <button class="btn btn-danger btn-sm" onclick="markVisitorCheckOut('2')">
                    <i data-lucide="log-out" style="width:12px;"></i> Check-out
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <!-- ========================================================================
       VIEW 1: REPAIR & MAINTENANCE JOB QUEUE (#view-repairs)
       ======================================================================== -->
  <main class="view-pane" id="view-repairs">
    <div class="container">
      <div class="spec-sheet-panel">
        <div class="panel-header-cluster">
          <div>
            <h2><i data-lucide="wrench" style="color:var(--amber);"></i> On-Ground Maintenance & Handyman Tasks</h2>
            <p style="font-size:13px; color:var(--text-muted);">Incoming repairs logged by residents. Mark job completed upon physical verification.</p>
          </div>
        </div>

        <div class="table-responsive-box">
          <table class="standard-table">
            <thead>
              <tr>
                <th>Ticket ID</th>
                <th>Category</th>
                <th>Target Unit</th>
                <th>Reported Problem</th>
                <th>Assigned Handyman</th>
                <th>Job Status</th>
                <th>Staff Action</th>
              </tr>
            </thead>
            <tbody id="tableBodyRepairQueue">
              <tr id="jobRow101">
                <td data-label="Ticket ID"><strong>#TKT-101</strong></td>
                <td data-label="Category">PLUMBING</td>
                <td data-label="Target Unit">Unit B-204 (Block B)</td>
                <td data-label="Problem">Main bathroom overhead supply pipe hairline leakage.</td>
                <td data-label="Handyman"><strong id="handyman101">Rafiq (Plumbing Staff)</strong></td>
                <td data-label="Status"><span class="badge-pill pending" id="badgeJob101">WORK IN PROGRESS</span></td>
                <td data-label="Action" id="actionJobCell101">
                  <button class="btn btn-emerald btn-sm" onclick="markJobComplete('101')">
                    <i data-lucide="check-check" style="width:12px;"></i> Mark Completed
                  </button>
                </td>
              </tr>
              <tr id="jobRow201">
                <td data-label="Ticket ID"><strong>#OWN-TKT-201</strong></td>
                <td data-label="Category">STRUCTURAL</td>
                <td data-label="Target Unit">Unit B-302 (Balcony)</td>
                <td data-label="Problem">Rainwater dampness and hairline fissures on exterior joint.</td>
                <td data-label="Handyman"><strong id="handyman201">Civil Contractor Team</strong></td>
                <td data-label="Status"><span class="badge-pill urgent" id="badgeJob201">AWAITING INSPECTION</span></td>
                <td data-label="Action" id="actionJobCell201">
                  <button class="btn btn-sky btn-sm" onclick="markJobComplete('201')">
                    <i data-lucide="check-check" style="width:12px;"></i> Complete Inspection
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <!-- ========================================================================
       VIEW 2: VEHICLE & PARKING MANAGEMENT (#view-parking)
       ======================================================================== -->
  <main class="view-pane" id="view-parking">
    <div class="container">
      <div class="spec-sheet-panel">
        <div class="panel-header-cluster">
          <div>
            <h2><i data-lucide="car" style="color:#0284c7;"></i> Parking Bay & Vehicle Clearance Registry</h2>
            <p style="font-size:13px; color:var(--text-muted);">Allocated resident parking slots and temporary visitor vehicle bay records.</p>
          </div>
          <button class="btn btn-sky" onclick="openModal('modalLogVehicle')">
            <i data-lucide="plus"></i> Register Guest Vehicle
          </button>
        </div>

        <div class="table-responsive-box">
          <table class="standard-table">
            <thead>
              <tr>
                <th>Parking Slot</th>
                <th>Allocated Unit</th>
                <th>Vehicle Type</th>
                <th>License Plate Number</th>
                <th>Driver / Resident Phone</th>
                <th>Bay Status</th>
              </tr>
            </thead>
            <tbody id="tableBodyParking">
              <tr>
                <td data-label="Slot"><strong>Slot P-101 (Basement 1)</strong></td>
                <td data-label="Unit">Unit A-401 (Owner)</td>
                <td data-label="Type">Sedan Car (Toyota)</td>
                <td data-label="Plate"><code>CHATTO-METRO-GA-12-3456</code></td>
                <td data-label="Contact"><code>01711-223344</code></td>
                <td data-label="Status"><span class="badge-pill active">PARKED</span></td>
              </tr>
              <tr>
                <td data-label="Slot"><strong>Slot P-204 (Basement 1)</strong></td>
                <td data-label="Unit">Unit B-204 (Tenant)</td>
                <td data-label="Type">SUV (Honda Vezel)</td>
                <td data-label="Plate"><code>DHAKA-METRO-GHA-98-7654</code></td>
                <td data-label="Contact"><code>01812-345678</code></td>
                <td data-label="Status"><span class="badge-pill active">PARKED</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <!-- ========================================================================
       VIEW 3: DUTY GUARD PROFILE (#view-profile)
       ======================================================================== -->
  <main class="view-pane" id="view-profile">
    <div class="container" style="max-width: 820px;">
      <div class="spec-sheet-panel">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border-subtle); padding-bottom:18px; margin-bottom:24px;">
          <div style="display:flex; align-items:center; gap:16px;">
            <div style="width: 52px; height: 52px; border-radius: var(--radius-full); background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; font-size: 18px; font-weight: 800; display:flex; align-items:center; justify-content:center; box-shadow: var(--shadow-sm);">
              <?= htmlspecialchars($sessionUser['initials'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div>
              <h2 style="font-size:22px; font-weight:800; line-height:1.2;"><?= htmlspecialchars($sessionUser['full_name'], ENT_QUOTES, 'UTF-8') ?></h2>
              <div style="font-size:12px; color:var(--text-muted); margin-top:2px;"><?= htmlspecialchars($sessionUser['duty_post'], ENT_QUOTES, 'UTF-8') ?> &bull; <?php echo date('D, d F Y'); ?></div>
            </div>
          </div>
          <span class="badge-pill active">DUTY ON-SHIFT</span>
        </div>

        <h3 style="font-size:17px; font-weight:800; margin-bottom:4px;">Duty Guard Credentials</h3>
        <p style="font-size:12px; color:var(--text-muted); margin-bottom:22px;">Security post details and personal credentials.</p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom:16px;">
          <div class="filter-ctrl">
            <label>Assigned Post</label>
            <input type="text" class="input-box" value="<?= htmlspecialchars($sessionUser['duty_post'], ENT_QUOTES, 'UTF-8') ?>" readonly style="opacity:0.85;">
          </div>
          <div class="filter-ctrl">
            <label>Active Shift</label>
            <input type="text" class="input-box" value="<?= htmlspecialchars($sessionUser['shift'], ENT_QUOTES, 'UTF-8') ?>" readonly style="opacity:0.85;">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom:24px;">
          <div class="filter-ctrl">
            <label>Control Room Terminal Phone</label>
            <input type="text" class="input-box" value="<?= htmlspecialchars($sessionUser['phone'], ENT_QUOTES, 'UTF-8') ?>" readonly style="opacity:0.85;">
          </div>
          <div class="filter-ctrl">
            <label>Duty Handover Password</label>
            <button class="btn btn-outline" style="height:46px; width:100%; margin-top:2px;" onclick="triggerToast('OTP dispatched to head supervisor.')">
              <i data-lucide="key" style="width:13px;"></i> Reset Post PIN
            </button>
          </div>
        </div>

        <div style="border-top: 1px solid var(--border-subtle); padding-top: 24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
          <span style="font-size:12px; color:var(--text-muted);">Guard shift logs are cryptographically timestamped.</span>
          <a href="../auth/logout.php" class="btn btn-danger btn-sm" style="display:flex; align-items:center; gap:6px;">
            <i data-lucide="log-out" style="width:14px;"></i> Log Out / Handover Shift
          </a>
        </div>
      </div>
    </div>
  </main>

  <!-- ========================================================================
       MODALS: (MANUAL WALK-IN ENTRY & GUEST VEHICLE LOG)
       ======================================================================== -->

  <!-- 1. Manual Walk-in Visitor Entry Modal -->
  <div class="modal-backdrop" id="modalManualVisitor">
    <div class="modal-card">
      <div class="modal-header-pic2">
        <h3>Manual Walk-In Gate Entry</h3>
        <button class="modal-close-btn" onclick="closeModal('modalManualVisitor')">
          <i data-lucide="x" style="width:20px; height:20px;"></i>
        </button>
      </div>
      <form class="modal-body-pic2" onsubmit="handleManualVisitorSubmit(event)">
        <div class="filter-ctrl">
          <label>VISITOR FULL NAME *</label>
          <input type="text" id="manName" class="input-box" required placeholder="Guest / Courier Rider Name">
        </div>

        <div class="filter-ctrl">
          <label>PHONE NUMBER *</label>
          <input type="text" id="manPhone" class="input-box" required placeholder="01XXXXXXXXX">
        </div>

        <div class="filter-ctrl">
          <label>DESTINATION APARTMENT *</label>
          <select id="manUnit" class="input-box" required>
            <option value="Unit A-401">Unit A-401 (Zubaer Hossain)</option>
            <option value="Unit B-204">Unit B-204 (Dr. Ariful Islam)</option>
            <option value="Unit B-302">Unit B-302 (Vacant)</option>
          </select>
        </div>

        <div class="filter-ctrl">
          <label>VISIT PURPOSE *</label>
          <input type="text" id="manPurpose" class="input-box" required placeholder="e.g. Food Delivery / Personal Guest">
        </div>

        <div class="modal-actions-pic2">
          <button type="button" class="btn-pic2-cancel" onclick="closeModal('modalManualVisitor')">Cancel</button>
          <button type="submit" class="btn-pic2-submit">Allow Gate Entry</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 2. Register Guest Vehicle Modal -->
  <div class="modal-backdrop" id="modalLogVehicle">
    <div class="modal-card">
      <div class="modal-header-pic2">
        <h3>Log Visitor Parking Allocation</h3>
        <button class="modal-close-btn" onclick="closeModal('modalLogVehicle')">
          <i data-lucide="x" style="width:20px; height:20px;"></i>
        </button>
      </div>
      <form class="modal-body-pic2" onsubmit="handleVehicleLogSubmit(event)">
        <div class="filter-ctrl">
          <label>VISITOR VEHICLE PLATE NUMBER *</label>
          <input type="text" id="vehPlate" class="input-box" required placeholder="e.g. CHATTO-METRO-GA-11-2233">
        </div>

        <div class="filter-ctrl">
          <label>ALLOCATE VISITOR BAY *</label>
          <select id="vehSlot" class="input-box">
            <option value="Guest Bay G-01 (Ground Floor)">Guest Bay G-01 (Ground Floor)</option>
            <option value="Guest Bay G-02 (Ground Floor)">Guest Bay G-02 (Ground Floor)</option>
            <option value="Visitor Basement Bay B-05">Visitor Basement Bay B-05</option>
          </select>
        </div>

        <div class="filter-ctrl">
          <label>VISITING RESIDENT UNIT *</label>
          <input type="text" id="vehUnit" class="input-box" required placeholder="e.g. Unit A-401">
        </div>

        <div class="modal-actions-pic2">
          <button type="button" class="btn-pic2-cancel" onclick="closeModal('modalLogVehicle')">Cancel</button>
          <button type="submit" class="btn-pic2-submit">Assign Parking</button>
        </div>
      </form>
    </div>
  </div>

  <div class="toast-pill" id="appToast">
    <i data-lucide="check-circle" style="color:var(--emerald);"></i>
    <span id="toastMessage">Done</span>
  </div>

  <!-- FOOTER -->
  <footer class="app-footer">
    <div class="container">
      <div class="footer-grid">
        <div>
          <div class="brand-cluster" onclick="routeTo('home')">
            <div class="brand-symbol">
              <i data-lucide="shield-alert"></i>
            </div>
            <span class="brand-title">Greenview Heights</span>
          </div>
          <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.65; max-width: 360px; margin-top: 14px;">
            Perimeter barrier clearance, on-ground repair coordination, and emergency response center.
          </p>
        </div>

        <div>
          <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-main); margin-bottom: 16px;">Quick Commands</h4>
          <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px;">
            <li><a onclick="routeTo('home')" style="color:var(--text-muted); text-decoration:none; cursor:pointer;">Gate Clearance</a></li>
            <li><a onclick="routeTo('repairs')" style="color:var(--text-muted); text-decoration:none; cursor:pointer;">Repair Jobs</a></li>
            <li><a onclick="routeTo('parking')" style="color:var(--text-muted); text-decoration:none; cursor:pointer;">Parking Registry</a></li>
          </ul>
        </div>

        <div>
          <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-main); margin-bottom: 16px;">Command & Contacts</h4>
          <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 10px;">
            <i data-lucide="map-pin" style="width: 15px; vertical-align: middle; color: #0284c7;"></i>
            <span>Gate 01 Control Room, Ground Level, Chittagong</span>
          </div>
          <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 10px;">
            <i data-lucide="phone-call" style="width: 15px; vertical-align: middle; color: #0284c7;"></i>
            <span>Control Intercom: Ext 101 / +880 1819-001122</span>
          </div>
        </div>
      </div>

      <div style="border-top: 1px solid var(--border-subtle); padding-top: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; font-size: 12px; color: var(--text-muted);">
        <div>&copy; <?php echo date('Y'); ?> Greenview Heights Society Security Command. All Rights Reserved.</div>
        <div>Guard Operations Portal &bull; Enterprise v3.2</div>
      </div>
    </div>
  </footer>

  <script>
    // In-memory counter state for active sessions
    let currentIngressCount = 14;
    let currentPendingJobs = 2;

    function applyTheme(theme) {
      const icon = document.getElementById('themeIcon');
      if (theme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        document.body.setAttribute('data-theme', 'dark');
        localStorage.setItem('greenview_theme', 'dark');
        if (icon) icon.setAttribute('data-lucide', 'sun');
      } else {
        document.documentElement.removeAttribute('data-theme');
        document.body.removeAttribute('data-theme');
        localStorage.setItem('greenview_theme', 'light');
        if (icon) icon.setAttribute('data-lucide', 'moon');
      }
      if (window.lucide) lucide.createIcons();
    }

    function toggleThemeMode() {
      const isDark = document.documentElement.getAttribute('data-theme') === 'dark' || document.body.getAttribute('data-theme') === 'dark';
      applyTheme(isDark ? 'light' : 'dark');
    }
    applyTheme(localStorage.getItem('greenview_theme') || 'light');

    function toggleNotificationDropdown() {
      const flyout = document.getElementById('notifFlyout');
      flyout.classList.toggle('active');
      document.getElementById('notifBadgeDot')?.remove();
    }

    document.addEventListener('click', (e) => {
      const flyout = document.getElementById('notifFlyout');
      const btn = document.getElementById('notifBellBtn');
      if (flyout && btn && !btn.contains(e.target) && !flyout.contains(e.target)) {
        flyout.classList.remove('active');
      }
    });

    window.routeTo = function(viewKey) {
      document.querySelectorAll('.view-pane').forEach(p => p.classList.remove('active'));
      document.querySelectorAll('.nav-tab-link').forEach(btn => btn.classList.remove('active'));
      document.querySelectorAll('.mobile-dock-btn').forEach(btn => btn.classList.remove('active'));

      const mapNav = {
        home: ['tabNavHome', 'mDockHome'],
        repairs: ['tabNavRepairs', 'mDockRepairs'],
        parking: ['tabNavParking', 'mDockParking'],
        profile: ['profileCircleBtn']
      };

      const targetPane = document.getElementById(`view-${viewKey}`);
      if (targetPane) targetPane.classList.add('active');

      if (mapNav[viewKey]) {
        mapNav[viewKey].forEach(id => document.getElementById(id)?.classList.add('active'));
      }

      window.scrollTo({ top: 0, behavior: 'smooth' });
      if (window.lucide) lucide.createIcons();
    };

    window.openModal = function(id) {
      document.getElementById(id)?.classList.add('active');
      if (window.lucide) lucide.createIcons();
    };

    window.closeModal = function(id) {
      document.getElementById(id)?.classList.remove('active');
    };

    function triggerToast(msg) {
      const toast = document.getElementById('appToast');
      const text = document.getElementById('toastMessage');
      if (!toast || !text) return;
      text.textContent = msg;
      toast.classList.add('active');
      setTimeout(() => toast.classList.remove('active'), 3500);
    }

    // 1. VERIFY 6-DIGIT OTP & QUICK CHECK-IN
    function handleVerifyOtp(e) {
      e.preventDefault();
      const code = document.getElementById('inputVerifyOtp').value.trim();
      const newRef = Math.floor(100 + Math.random() * 900);
      
      const tbody = document.getElementById('tableBodyVisitors');
      const tr = document.createElement('tr');
      tr.id = `visRow${newRef}`;
      tr.innerHTML = `
        <td data-label="Pass Ref"><strong>#VIS-${newRef}</strong></td>
        <td data-label="Visitor">Approved OTP Guest</td>
        <td data-label="Phone"><code>Verified Mobile</code></td>
        <td data-label="Unit">Unit A-401</td>
        <td data-label="OTP"><strong style="font-family:var(--font-mono); color:var(--primary-accent); letter-spacing:1px;">${code}</strong></td>
        <td data-label="Timestamp"><?php echo date('Y-m-d H:i:s'); ?></td>
        <td data-label="Status"><span class="badge-pill active" id="badgeVis${newRef}">CHECKED_IN</span></td>
        <td data-label="Action" id="actionVisCell${newRef}">
          <button class="btn btn-danger btn-sm" onclick="markVisitorCheckOut('${newRef}')">
            <i data-lucide="log-out" style="width:12px;"></i> Check-out
          </button>
        </td>
      `;
      tbody.prepend(tr);

      // Increment Ingress Counter
      currentIngressCount++;
      document.getElementById('statCheckedInCount').textContent = `${currentIngressCount} Ingress`;

      triggerToast(`OTP ${code} Verified! Perimeter boom barrier opened.`);
      document.getElementById('inputVerifyOtp').value = '';
      if (window.lucide) lucide.createIcons();
    }

    // 2. CHECK-OUT VISITOR & BUTTON TRANSITION (FIXED LOGIC)
    function markVisitorCheckOut(id) {
      const badge = document.getElementById(`badgeVis${id}`);
      const actionCell = document.getElementById(`actionVisCell${id}`);

      if (badge) {
        badge.className = 'badge-pill checked-out';
        badge.textContent = 'CHECKED_OUT';
      }

      // Replace Check-out button with Exited indicator
      if (actionCell) {
        actionCell.innerHTML = `
          <span style="font-size:12px; font-weight:700; color:var(--text-muted); display:inline-flex; align-items:center; gap:4px;">
            <i data-lucide="check" style="width:13px; color:var(--emerald);"></i> Exited Gate
          </span>
        `;
      }

      // Decrement Ingress Counter
      if (currentIngressCount > 0) {
        currentIngressCount--;
        document.getElementById('statCheckedInCount').textContent = `${currentIngressCount} Ingress`;
      }

      triggerToast(`Visitor #${id} successfully logged checked-out.`);
      if (window.lucide) lucide.createIcons();
    }

    // 3. REPAIR COMPLETION & BUTTON TRANSITION (FIXED LOGIC)
    function markJobComplete(id) {
      const badge = document.getElementById(`badgeJob${id}`);
      const actionCell = document.getElementById(`actionJobCell${id}`);

      if (badge) {
        badge.className = 'badge-pill active';
        badge.textContent = 'JOB COMPLETED';
      }

      // Replace Active button with Resolved indicator
      if (actionCell) {
        actionCell.innerHTML = `
          <span style="font-size:12px; font-weight:800; color:var(--emerald); display:inline-flex; align-items:center; gap:4px;">
            <i data-lucide="check-circle-2" style="width:14px;"></i> Resolved
          </span>
        `;
      }

      // Decrement Pending Repairs Counter
      if (currentPendingJobs > 0) {
        currentPendingJobs--;
        document.getElementById('statPendingJobsCount').textContent = `${currentPendingJobs} Active`;
        if (currentPendingJobs === 0) {
          document.getElementById('repairTabBadge')?.remove();
        }
      }

      triggerToast(`Repair Job #${id} marked completed! Completion alert pushed to resident & council.`);
      if (window.lucide) lucide.createIcons();
    }

    // 4. MANUAL WALK-IN VISITOR
    function handleManualVisitorSubmit(e) {
      e.preventDefault();
      const name = document.getElementById('manName').value;
      const phone = document.getElementById('manPhone').value;
      const unit = document.getElementById('manUnit').value;
      const newRef = Math.floor(100 + Math.random() * 900);

      const tbody = document.getElementById('tableBodyVisitors');
      const tr = document.createElement('tr');
      tr.id = `visRow${newRef}`;
      tr.innerHTML = `
        <td data-label="Pass Ref"><strong>#VIS-MAN-${newRef}</strong></td>
        <td data-label="Visitor">${name}</td>
        <td data-label="Phone"><code>${phone}</code></td>
        <td data-label="Unit">${unit}</td>
        <td data-label="OTP"><strong style="font-family:var(--font-mono); color:var(--primary-accent); letter-spacing:1px;">WALK-IN</strong></td>
        <td data-label="Timestamp"><?php echo date('Y-m-d H:i:s'); ?></td>
        <td data-label="Status"><span class="badge-pill active" id="badgeVis${newRef}">CHECKED_IN</span></td>
        <td data-label="Action" id="actionVisCell${newRef}">
          <button class="btn btn-danger btn-sm" onclick="markVisitorCheckOut('${newRef}')">
            <i data-lucide="log-out" style="width:12px;"></i> Check-out
          </button>
        </td>
      `;
      tbody.prepend(tr);

      currentIngressCount++;
      document.getElementById('statCheckedInCount').textContent = `${currentIngressCount} Ingress`;

      closeModal('modalManualVisitor');
      triggerToast(`Walk-in visitor ${name} allowed entry to ${unit}.`);
      e.target.reset();
      if (window.lucide) lucide.createIcons();
    }

    // 5. REGISTER GUEST VEHICLE (CLEAN ROW WITHOUT CALL BUTTON)
    function handleVehicleLogSubmit(e) {
      e.preventDefault();
      const plate = document.getElementById('vehPlate').value;
      const slot = document.getElementById('vehSlot').value;
      const unit = document.getElementById('vehUnit').value;

      const tbody = document.getElementById('tableBodyParking');
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td data-label="Slot"><strong>${slot}</strong></td>
        <td data-label="Unit">${unit} (Visitor)</td>
        <td data-label="Type">Visitor Car</td>
        <td data-label="Plate"><code>${plate}</code></td>
        <td data-label="Contact">Gate Verified</td>
        <td data-label="Status"><span class="badge-pill active">PARKED</span></td>
      `;
      tbody.prepend(tr);

      closeModal('modalLogVehicle');
      triggerToast(`Vehicle ${plate} assigned to ${slot}.`);
      e.target.reset();
      if (window.lucide) lucide.createIcons();
    }

    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) lucide.createIcons();
    });
  </script>
</body>
</html>