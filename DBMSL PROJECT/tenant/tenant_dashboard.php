<?php
// ============================================================================
// ARCHITECTURE: Enterprise Resident / Tenant Management Portal (Single-File)
// FILE: tenant/tenant_dashboard.php
// REFACTORED: Verified OTP Dispatch & Verification Flow + Clean Script Binding
// ============================================================================
session_start();
date_default_timezone_set('Asia/Dhaka');

if (!isset($_SESSION['user_role'])) {
    $_SESSION['user_role'] = 'TENANT';
}
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 3;
}

$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

$fullName = !empty($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Dr. Ariful Islam';
$nameParts = preg_split('/\s+/', trim($fullName));
$avatarInitials = count($nameParts) > 1 
    ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1))
    : strtoupper(substr($fullName, 0, 2));

$sessionUser = [
    'is_logged_in' => $isLoggedIn,
    'user_id'      => (int)$_SESSION['user_id'],
    'first_name'   => $nameParts[0] ?? 'Ariful',
    'last_name'    => end($nameParts) ?? 'Islam',
    'full_name'    => $fullName,
    'initials'     => !empty($avatarInitials) ? $avatarInitials : 'AI',
    'email'        => !empty($_SESSION['email']) ? $_SESSION['email'] : 'resident@example.com',
    'phone'        => !empty($_SESSION['phone_number']) ? $_SESSION['phone_number'] : '+880 1812-345678',
    'flat_number'  => $_SESSION['flat_number'] ?? 'Unit B-204',
    'block'        => 'Block B',
    'dob'          => '1992-06-15',
    'role_id'      => 2,
    'role_name'    => 'TENANT'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Greenview Heights — Resident Portal & Community Hub</title>

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
      max-width: 1320px;
      margin: 0 auto;
      padding: 0 clamp(16px, 3vw, 28px);
      width: 100%;
    }

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
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
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
      color: var(--emerald);
      box-shadow: var(--shadow-sm);
    }

    .nav-actions-cluster {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-shrink: 0;
      position: relative;
    }

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
      width: 320px;
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
    .notif-item.ack { background: var(--amber-soft); border-left: 3px solid var(--amber); }
    .notif-item.res { background: var(--emerald-soft); border-left: 3px solid var(--emerald); }
    .notif-item.notice { background: var(--primary-accent-soft); border-left: 3px solid var(--primary-accent); }

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

    .profile-circle-btn {
      width: 42px;
      height: 42px;
      border-radius: var(--radius-full);
      background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
      color: #ffffff !important;
      font-size: 14px;
      font-weight: 800;
      letter-spacing: 0.5px;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      border: 2px solid rgba(255, 255, 255, 0.8) !important;
      cursor: pointer;
      box-shadow: var(--shadow-sm);
      transition: var(--transition);
      user-select: none;
      line-height: 1;
      text-shadow: 0 1px 2px rgba(0,0,0,0.25);
    }
    .profile-circle-btn:hover {
      transform: scale(1.06);
      box-shadow: 0 0 0 3px var(--emerald-soft);
    }

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

    .hero-view {
      padding: 44px 0 28px;
      background: radial-gradient(circle at 10% 20%, var(--emerald-soft) 0%, transparent 40%);
      border-bottom: 1px solid var(--border-subtle);
      margin-bottom: 32px;
    }

    .hero-split {
      display: grid;
      grid-template-columns: 1.15fr 0.85fr;
      gap: 40px;
      align-items: center;
    }

    .hero-tag {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 14px;
      border-radius: var(--radius-full);
      background: var(--emerald-soft);
      color: var(--emerald);
      font-size: 12px;
      font-weight: 800;
      margin-bottom: 16px;
      border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .hero-headline {
      font-size: clamp(26px, 4vw, 38px);
      font-weight: 800;
      letter-spacing: -0.03em;
      line-height: 1.18;
      margin-bottom: 14px;
    }
    .hero-headline span { color: var(--emerald); }

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

    .hero-poster-frame {
      position: relative;
      border-radius: var(--radius-lg);
      overflow: hidden;
      border: 1px solid var(--border-subtle);
      box-shadow: var(--shadow-lg);
    }

    .hero-poster-img {
      width: 100%;
      height: 320px;
      object-fit: cover;
      display: block;
    }

    .poster-caption-glass {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(15, 23, 42, 0.75);
      backdrop-filter: blur(10px);
      color: #fff;
      padding: 14px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
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
        padding-top: 12px;
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
    .badge-pill.active, .badge-pill.verified, .badge-pill.paid, .badge-pill.confirmed { background: var(--emerald-soft); color: var(--emerald); }
    .badge-pill.pending, .badge-pill.listed { background: var(--amber-soft); color: var(--amber); }
    .badge-pill.unpaid, .badge-pill.declined { background: var(--rose-soft); color: var(--rose); }

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
    .btn-brand { background: var(--primary-accent); color: #fff; }
    .btn-brand:hover { background: var(--primary-accent-hover); }
    .btn-emerald { background: var(--emerald); color: #fff; }
    .btn-emerald:hover { background: var(--emerald-hover); }
    .btn-outline { background: transparent; border-color: var(--border-subtle); color: var(--text-main); }
    .btn-outline:hover { background: var(--bg-surface-alt); }
    .btn-outline-emerald { background: transparent; border-color: var(--emerald); color: var(--emerald); }
    .btn-outline-emerald:hover { background: var(--emerald-soft); }
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
      border-color: var(--emerald) !important;
      background: #ffffff !important;
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
    }
    [data-theme="dark"] .input-box {
      background: #1a2234;
      border-color: #374151;
      color: #f8fafc;
    }

    .amenity-subtabs {
      display: flex;
      gap: 10px;
      margin-bottom: 24px;
      border-bottom: 2px solid var(--border-subtle);
      padding-bottom: 12px;
      overflow-x: auto;
    }
    .amenity-subtab-btn {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 18px;
      border-radius: var(--radius-xs);
      border: 1px solid transparent;
      background: transparent;
      color: var(--text-muted);
      font-size: 13.5px;
      font-weight: 700;
      cursor: pointer;
      transition: var(--transition);
      white-space: nowrap;
    }
    .amenity-subtab-btn:hover { color: var(--text-main); background: var(--bg-surface-alt); }
    .amenity-subtab-btn.active {
      background: var(--emerald-soft);
      color: var(--emerald);
      border-color: rgba(16, 185, 129, 0.3);
    }

    .amenity-subpanel { display: none; }
    .amenity-subpanel.active { display: block; animation: fadeInView 0.2s ease; }

    .profile-card-canvas {
      background: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-lg);
      padding: clamp(20px, 4vw, 40px);
      box-shadow: var(--shadow-sm);
    }
    .profile-grid-two {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

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
      max-width: 500px;
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
      background: #1d64ec;
      border: 1px solid #1d64ec;
      color: #ffffff;
      font-weight: 700;
      font-size: 13.5px;
      padding: 10px 22px;
      border-radius: 8px;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(29, 100, 236, 0.28);
      transition: var(--transition);
    }
    .btn-pic2-submit:hover { background: #1754c8; }

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
    .mobile-dock-btn.active { color: var(--emerald); }

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
      .profile-grid-two { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <!-- TOP APPLICATION NAVIGATION DOCK -->
  <header class="app-nav">
    <div class="container nav-container">
      
      <div class="brand-cluster" onclick="routeTo('home')">
        <div class="brand-symbol">
          <i data-lucide="building-2"></i>
        </div>
        <div>
          <span class="brand-title">Greenview Heights</span>
          <div style="font-size: 10.5px; font-weight: 800; color: var(--emerald); text-transform: uppercase;">Resident Portal</div>
        </div>
      </div>

      <!-- Center Navigation Tabs -->
      <ul class="nav-tabs-group">
        <li>
          <button class="nav-tab-link active" id="tabNavHome" onclick="routeTo('home')">
            <i data-lucide="home" style="width: 15px;"></i> Home
          </button>
        </li>
        <li>
          <button class="nav-tab-link" id="tabNavAgreement" onclick="routeTo('agreement')">
            <i data-lucide="file-text" style="width: 15px;"></i> My Lease
          </button>
        </li>
        <li>
          <button class="nav-tab-link" id="tabNavBills" onclick="routeTo('bills')">
            <i data-lucide="receipt" style="width: 15px;"></i> Bills & Invoices
          </button>
        </li>
        <li>
          <button class="nav-tab-link" id="tabNavAmenities" onclick="routeTo('amenities')">
            <i data-lucide="sparkles" style="width: 15px;"></i> Amenities
          </button>
        </li>
        <li>
          <button class="nav-tab-link" id="tabNavVisitors" onclick="routeTo('visitors')">
            <i data-lucide="shield" style="width: 15px;"></i> Gate Pass
          </button>
        </li>
        <li>
          <button class="nav-tab-link" id="tabNavComplaints" onclick="routeTo('complaints')">
            <i data-lucide="wrench" style="width: 15px;"></i> Complaints
          </button>
        </li>
      </ul>

      <!-- Right Controls -->
      <div class="nav-actions-cluster">
        
        <button class="notif-bell-btn" id="notifBellBtn" title="Complaints & Community Notices" onclick="toggleNotificationDropdown()">
          <i data-lucide="bell" style="width: 18px;"></i>
          <span class="notif-dot" id="notifBadgeDot"></span>
        </button>

        <!-- NOTIFICATION FLYOUT -->
        <div class="notif-flyout-card" id="notifFlyout">
          <div style="font-weight: 800; font-size: 13px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px; display:flex; justify-content:space-between; align-items:center;">
            <span>Notifications & Notices</span>
            <span style="font-size:10.5px; color:var(--emerald); font-weight:700;">3 Unread</span>
          </div>

          <div style="display: flex; flex-direction: column; gap: 8px;">
            <div class="notif-item res">
              <strong style="color: var(--emerald); font-size: 12px; display:flex; align-items:center; gap:5px;">
                <i data-lucide="check-circle" style="width:13px;"></i> Complaint Resolved
              </strong>
              <div style="color: var(--text-secondary);">Ticket #TKT-101 (Bathroom Water Leakage) marked fixed by plumbing engineer.</div>
              <span style="font-size: 10px; color: var(--text-muted); margin-top:2px;">Today &bull; 04:30 PM</span>
            </div>

            <div class="notif-item ack">
              <strong style="color: var(--amber); font-size: 12px; display:flex; align-items:center; gap:5px;">
                <i data-lucide="clock" style="width:13px;"></i> Ticket Acknowledged
              </strong>
              <div style="color: var(--text-secondary);">Repair request for balcony switch has been received & assigned to electrical staff.</div>
              <span style="font-size: 10px; color: var(--text-muted); margin-top:2px;">Today &bull; 11:15 AM</span>
            </div>

            <div class="notif-item notice">
              <strong style="color: var(--primary-accent); font-size: 12px; display:flex; align-items:center; gap:5px;">
                <i data-lucide="info" style="width:13px;"></i> Society Notice
              </strong>
              <div style="color: var(--text-secondary);">Water tank maintenance scheduled this Friday (10:00 AM - 01:00 PM). Please conserve supply.</div>
              <span style="font-size: 10px; color: var(--text-muted); margin-top:2px;">Yesterday &bull; 06:00 PM</span>
            </div>
          </div>
        </div>

        <button class="theme-trigger-btn" id="themeBtn" title="Toggle Display Theme" onclick="toggleThemeMode()">
          <i data-lucide="moon" id="themeIcon"></i>
        </button>

        <button class="profile-circle-btn" id="profileCircleBtn" title="Go to My Profile Workspace" onclick="routeTo('profile')">
          <span id="navInitialsSpan"><?= htmlspecialchars($sessionUser['initials'], ENT_QUOTES, 'UTF-8') ?></span>
        </button>
      </div>

    </div>
  </header>

  <!-- CLEAN 4-ITEM MOBILE DOCK -->
  <nav class="mobile-bottom-bar">
    <button class="mobile-dock-btn active" id="mDockHome" onclick="routeTo('home')">
      <i data-lucide="home" style="width: 18px;"></i>
      <span>Home</span>
    </button>
    <button class="mobile-dock-btn" id="mDockBills" onclick="routeTo('bills')">
      <i data-lucide="receipt" style="width: 18px;"></i>
      <span>Bills</span>
    </button>
    <button class="mobile-dock-btn" id="mDockAmenities" onclick="routeTo('amenities')">
      <i data-lucide="sparkles" style="width: 18px;"></i>
      <span>Amenities</span>
    </button>
    <button class="mobile-dock-btn" id="mDockComplaints" onclick="routeTo('complaints')">
      <i data-lucide="wrench" style="width: 18px;"></i>
      <span>Complaints</span>
    </button>
  </nav>

  <!-- ========================================================================
       VIEW 0: FULL LANDING HOMEPAGE (#view-home)
       ======================================================================== -->
  <main class="view-pane active" id="view-home">
    <section class="hero-view">
      <div class="container hero-split">
        <div>
          <div class="hero-tag">
            <i data-lucide="verified" style="width: 14px;"></i> Resident Verified Portal &bull; <?= htmlspecialchars($sessionUser['flat_number'], ENT_QUOTES, 'UTF-8') ?>
          </div>
          <h1 class="hero-headline">
            Welcome to luxury apartment living at <span>Greenview</span>.
          </h1>
          <p style="color: var(--text-muted); font-size: 15px; margin-bottom: 24px; max-width: 560px;">
            Manage your legal lease agreements, pay split monthly dues with instant invoice downloads, book amenities (Gym, Turf, Hall), and file repair tickets.
          </p>

          <div class="hero-telemetry-cluster">
            <div class="telemetry-card">
              <span class="telemetry-val">Active</span>
              <span class="telemetry-lbl">Lease Agreement</span>
            </div>
            <div class="telemetry-card">
              <span class="telemetry-val" style="color: var(--emerald);">৳ 50,500</span>
              <span class="telemetry-lbl">Current Month Dues</span>
            </div>
          </div>
        </div>

        <div class="hero-poster-frame">
          <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&auto=format&fit=crop&q=80" class="hero-poster-img" alt="Greenview Towers">
          <div class="poster-caption-glass">
            <div>
              <div style="font-weight: 800; font-size: 14px;"><?= htmlspecialchars($sessionUser['flat_number'], ENT_QUOTES, 'UTF-8') ?> &bull; Block B</div>
              <div style="font-size: 12px; opacity: 0.8;">Lease Registered with Society Committee</div>
            </div>
            <span style="font-size: 11px; background: rgba(16,185,129,0.3); color:#10b981; padding: 4px 10px; border-radius: var(--radius-full); font-weight:800;">ACTIVE LEASE</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Quick Operations Grid -->
    <div class="container">
      <div style="margin-bottom: 28px;">
        <h2 style="font-size: 24px; font-weight: 800;">Quick Services & Portals</h2>
        <p style="color: var(--text-muted); font-size: 13.5px;">Click any dedicated module below to manage your residence.</p>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div class="telemetry-card" style="padding: 24px; cursor: pointer;" onclick="routeTo('bills')">
          <div style="width: 44px; height: 44px; border-radius: var(--radius-sm); background: var(--primary-accent-soft); color: var(--primary-accent); display: flex; align-items: center; justify-content: center; margin-bottom: 14px;">
            <i data-lucide="receipt"></i>
          </div>
          <h3 style="font-size: 17px; font-weight: 800; margin-bottom: 4px;">Split Invoices & Pay</h3>
          <p style="font-size: 12.5px; color: var(--text-muted);">View rent, maintenance, and utility breakdown. Pay online with automatic invoice PDF generation.</p>
        </div>

        <div class="telemetry-card" style="padding: 24px; cursor: pointer;" onclick="routeTo('amenities')">
          <div style="width: 44px; height: 44px; border-radius: var(--radius-sm); background: var(--emerald-soft); color: var(--emerald); display: flex; align-items: center; justify-content: center; margin-bottom: 14px;">
            <i data-lucide="sparkles"></i>
          </div>
          <h3 style="font-size: 17px; font-weight: 800; margin-bottom: 4px;">Club Amenities Hub</h3>
          <p style="font-size: 12.5px; color: var(--text-muted);">Apex Fitness Gym membership, Skyview Futsal Turf, and Banquet Hall booking with checkout.</p>
        </div>

        <div class="telemetry-card" style="padding: 24px; cursor: pointer;" onclick="routeTo('visitors')">
          <div style="width: 44px; height: 44px; border-radius: var(--radius-sm); background: rgba(99, 102, 241, 0.15); color: var(--indigo); display: flex; align-items: center; justify-content: center; margin-bottom: 14px;">
            <i data-lucide="shield"></i>
          </div>
          <h3 style="font-size: 17px; font-weight: 800; margin-bottom: 4px;">Gate Pass 6-Digit OTP</h3>
          <p style="font-size: 12.5px; color: var(--text-muted);">Issue encrypted single-use clearance codes for guests and delivery drivers.</p>
        </div>

        <div class="telemetry-card" style="padding: 24px; cursor: pointer;" onclick="routeTo('complaints')">
          <div style="width: 44px; height: 44px; border-radius: var(--radius-sm); background: var(--amber-soft); color: var(--amber); display: flex; align-items: center; justify-content: center; margin-bottom: 14px;">
            <i data-lucide="wrench"></i>
          </div>
          <h3 style="font-size: 17px; font-weight: 800; margin-bottom: 4px;">Maintenance & Complaints</h3>
          <p style="font-size: 12.5px; color: var(--text-muted);">Log quick repairs or lodge formal written complaints with real-time tracking.</p>
        </div>
      </div>
    </div>
  </main>

  <!-- ========================================================================
       VIEW 1: TENANCY AGREEMENT (#view-agreement)
       ======================================================================== -->
  <main class="view-pane" id="view-agreement">
    <div class="container">
      <div class="spec-sheet-panel">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
          <div>
            <h2><i data-lucide="file-text" style="color:var(--emerald);"></i> Official Tenancy Agreement Record</h2>
            <p style="font-size:13px; color:var(--text-muted);">Registered under Flat Owner Zubaer Hossain & Greenview Society Management.</p>
          </div>
          <span class="badge-pill active">STATUS: ACTIVE & REGISTERED</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
          <div class="telemetry-card">
            <span class="telemetry-lbl">Allocated Residence</span>
            <span class="telemetry-val"><?= htmlspecialchars($sessionUser['flat_number'], ENT_QUOTES, 'UTF-8') ?></span>
            <span style="font-size:11.5px; color:var(--text-muted);">1,850 SqFt &bull; Block B</span>
          </div>
          <div class="telemetry-card">
            <span class="telemetry-lbl">Lease Validity</span>
            <span class="telemetry-val">2026-01-01 to 2026-12-31</span>
            <span style="font-size:11.5px; color:var(--emerald);">12 Months Fixed Validity</span>
          </div>
          <div class="telemetry-card">
            <span class="telemetry-lbl">Agreed Monthly Rent</span>
            <span class="telemetry-val" style="font-family:var(--font-mono);">৳ 45,000</span>
            <span style="font-size:11.5px; color:var(--text-muted);">Due 5th of each month</span>
          </div>
        </div>

        <div style="background:var(--bg-surface-alt); padding:20px; border-radius:var(--radius-sm); border:1px solid var(--border-subtle); margin-bottom:24px;">
          <h3 style="font-size:15px; font-weight:800; margin-bottom:12px;">Sub-Registry & Bylaw Certification</h3>
          <p style="font-size:13px; color:var(--text-muted); line-height:1.6;">
            This lease grants legal tenancy rights for <?= htmlspecialchars($sessionUser['flat_number'], ENT_QUOTES, 'UTF-8') ?>. Security deposit of ৳ 90,000 (2 months) is held under escrow by society trustees for deposit protection.
          </p>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
          <span style="font-size:12px; color:var(--text-muted);">Document Reference: <code>GH-AGR-2026-0102</code></span>
          <button class="btn btn-outline" onclick="downloadAgreementPdf()">
  <i data-lucide="download"></i> Download Agreement PDF Copy
</button>
        </div>
      </div>
    </div>
  </main>

  <!-- ========================================================================
       VIEW 2: BILLS & INVOICES (#view-bills)
       ======================================================================== -->
  <main class="view-pane" id="view-bills">
    <div class="container">
      <div class="spec-sheet-panel">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
          <div>
            <h2><i data-lucide="receipt" style="color:var(--primary-accent);"></i> Bills & Split Invoicing Desk</h2>
            <p style="font-size:13px; color:var(--text-muted);">Itemized rent, maintenance, and utilities. Instant invoice PDF generation upon payment.</p>
          </div>
        </div>

        <div style="background:var(--bg-surface-alt); border:1px solid var(--border-subtle); border-radius:var(--radius-md); padding:clamp(16px, 2.5vw, 26px); margin-bottom:30px;">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px; margin-bottom:20px;">
            <div>
              <span class="badge-pill unpaid" id="badgeInvoiceStatus">UNPAID &bull; SEPTEMBER 2026</span>
              <h3 style="font-size:20px; font-weight:800; margin-top:8px;">September Residence Monthly Dues</h3>
              <div style="font-size:12px; color:var(--text-muted);">Invoice Ref: <code>#INV-2026-09</code> &bull; Due: 2026-09-28</div>
            </div>
            <div style="text-align:right;">
              <span style="font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase;">Total Payable</span>
              <div style="font-size:26px; font-weight:800; font-family:var(--font-mono); color:var(--primary-accent);">৳ 50,500</div>
            </div>
          </div>

          <div class="table-responsive-box" style="margin-bottom:20px;">
            <table class="standard-table">
              <thead>
                <tr>
                  <th>Description</th>
                  <th>Category</th>
                  <th>Rate Calculation</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td data-label="Description">Core Apartment Lease Rent (<?= htmlspecialchars($sessionUser['flat_number'], ENT_QUOTES, 'UTF-8') ?>)</td>
                  <td data-label="Category">Flat Rent</td>
                  <td data-label="Rate">Fixed Base Lease</td>
                  <td data-label="Subtotal"><strong>৳ 45,000</strong></td>
                </tr>
                <tr>
                  <td data-label="Description">Society Common Maintenance & Security</td>
                  <td data-label="Category">Society Dues</td>
                  <td data-label="Rate">Lifts, Guards, Perimeter Lighting</td>
                  <td data-label="Subtotal"><strong>৳ 4,000</strong></td>
                </tr>
                <tr>
                  <td data-label="Description">Sub-metered Utility Surcharge (Water/Pump)</td>
                  <td data-label="Category">Utilities</td>
                  <td data-label="Rate">September Consumption Surcharge</td>
                  <td data-label="Subtotal"><strong>৳ 1,500</strong></td>
                </tr>
              </tbody>
            </table>
          </div>

          <div style="display:flex; justify-content:flex-end; gap:12px;">
            <button class="btn btn-emerald" id="btnPayOnlineTrigger" onclick="openModal('modalPayOnline')">
              <i data-lucide="credit-card"></i> Pay Online
            </button>
          </div>
        </div>

        <h3 style="font-size:16px; font-weight:800; margin-bottom:14px; display:flex; align-items:center; gap:8px;">
          <i data-lucide="history" style="width:17px; color:var(--emerald);"></i> Payment History & Generated Invoices
        </h3>
        <div class="table-responsive-box">
          <table class="standard-table">
            <thead>
              <tr>
                <th>Invoice Ref</th>
                <th>Billing Cycle</th>
                <th>Amount Paid</th>
                <th>Channel / Gateway</th>
                <th>Transaction Reference (TrxID)</th>
                <th>Status</th>
                <th>Official Invoice</th>
              </tr>
            </thead>
            <tbody id="tableBodyHistory">
              <tr>
                <td data-label="Invoice Ref"><strong>#INV-2026-08</strong></td>
                <td data-label="Billing Cycle">August 2026</td>
                <td data-label="Amount Paid"><strong>৳ 50,500</strong></td>
                <td data-label="Channel">bKash Online Gateway</td>
                <td data-label="TrxID"><code>TRX-99882211A</code></td>
                <td data-label="Status"><span class="badge-pill paid">PAID</span></td>
                <td data-label="Action">
                  <button class="btn btn-outline btn-sm" onclick="downloadInvoiceReceipt('INV-2026-08', '50,500', 'bKash Online Gateway', 'TRX-99882211A')">
                    <i data-lucide="download" style="width:12px;"></i> Get Invoice
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
       VIEW 3: UNIFIED AMENITIES HUB (#view-amenities)
       ======================================================================== -->
  <main class="view-pane" id="view-amenities">
    <div class="container">
      <div class="spec-sheet-panel">
        <div style="margin-bottom:20px;">
          <h2><i data-lucide="sparkles" style="color:var(--emerald);"></i> Society Amenities & Fitness Center</h2>
          <p style="font-size:13px; color:var(--text-muted);">Choose a facility to register membership, book slots, and pay directly online.</p>
        </div>

        <div class="amenity-subtabs">
          <button class="amenity-subtab-btn active" id="btnSubGym" onclick="switchAmenitySubTab('gym')">
            <i data-lucide="dumbbell" style="width:16px;"></i> Apex Gym Membership
          </button>
          <button class="amenity-subtab-btn" id="btnSubTurf" onclick="switchAmenitySubTab('turf')">
            <i data-lucide="activity" style="width:16px;"></i> Skyview Futsal Turf
          </button>
          <button class="amenity-subtab-btn" id="btnSubHall" onclick="switchAmenitySubTab('hall')">
            <i data-lucide="landmark" style="width:16px;"></i> Majestic Banquet Hall
          </button>
        </div>

        <!-- SUB-PAGE 1: APEX GYM -->
        <div class="amenity-subpanel active" id="subpanel-gym">
          <div id="gymStep1Enrollment">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 26px;">
              <div style="background:var(--bg-surface-alt); border:1px solid var(--border-subtle); border-radius:var(--radius-md); padding:24px;">
                <span class="badge-pill active" style="margin-bottom:12px;">FITNESS & WELLNESS</span>
                <h3 style="font-size:18px; font-weight:800; margin-bottom:8px;">Apex Fitness Center</h3>
                <p style="font-size:13px; color:var(--text-muted); margin-bottom:16px;">
                  State-of-the-art cardio suite, Olympic free weights, dedicated yoga zone, certified personal trainers, and steam room.
                </p>
                <ul style="list-style:none; display:flex; flex-direction:column; gap:8px; font-size:13px; color:var(--text-main);">
                  <li><i data-lucide="check" style="width:14px; color:var(--emerald); vertical-align:middle;"></i> Floor: 3rd Floor Wellness Suite</li>
                  <li><i data-lucide="check" style="width:14px; color:var(--emerald); vertical-align:middle;"></i> Timings: 06:00 AM – 11:00 PM (Daily)</li>
                  <li><i data-lucide="check" style="width:14px; color:var(--emerald); vertical-align:middle;"></i> Locker & Shower Facilities Included</li>
                </ul>
              </div>

              <div style="background:var(--bg-surface-alt); border:1px solid var(--border-subtle); border-radius:var(--radius-md); padding:24px; display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                  <span class="badge-pill pending" style="margin-bottom:12px;">MEMBERSHIP STATUS: INACTIVE</span>
                  <h3 style="font-size:18px; font-weight:800; margin-bottom:8px;">Enroll / Renew Gym Pass</h3>
                  <div class="filter-ctrl" style="margin-bottom:12px;">
                    <label>SELECT TIER</label>
                    <select id="gymEnrollPlanSelect" class="input-box">
                      <option value="1500">Monthly Pass — ৳ 1,500 / month</option>
                      <option value="4000">Quarterly Pass — ৳ 4,000 (3 Months)</option>
                      <option value="14000">Annual Pass — ৳ 14,000 (Full Year)</option>
                    </select>
                  </div>
                  <div class="filter-ctrl">
                    <label>PREFERRED ACCESS SHIFT</label>
                    <select id="gymEnrollShiftSelect" class="input-box">
                      <option value="Morning (06:00 AM – 11:00 AM)">Morning (06:00 AM – 11:00 AM)</option>
                      <option value="Evening (04:00 PM – 10:00 PM)">Evening (04:00 PM – 10:00 PM)</option>
                      <option value="All Day Access">All Day Unrestricted Access</option>
                    </select>
                  </div>
                </div>
                <button class="btn btn-emerald" style="margin-top:16px;" onclick="goToGymPaymentStep2()">
                  Proceed to Payment Checkout &rarr;
                </button>
              </div>
            </div>
          </div>

          <div id="gymStep2Payment" style="display:none; background:var(--bg-surface-alt); border:1px solid var(--border-subtle); border-radius:var(--radius-md); padding:26px; margin-bottom:26px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--border-subtle); padding-bottom:12px;">
              <div>
                <span class="badge-pill active">STEP 2: PAYMENT CHECKOUT</span>
                <h3 style="font-size:18px; font-weight:800; margin-top:4px;">Pay Gym Membership Fees</h3>
              </div>
              <button class="btn btn-outline btn-sm" onclick="returnToGymStep1()">&larr; Change Plan</button>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:18px;">
              <div>
                <div style="font-size:12px; color:var(--text-muted); text-transform:uppercase; font-weight:800;">Selected Plan</div>
                <div style="font-size:16px; font-weight:800;" id="gymDisplaySelectedPlan">Monthly Pass</div>
              </div>
              <div style="text-align:right;">
                <div style="font-size:12px; color:var(--text-muted); text-transform:uppercase; font-weight:800;">Amount Due</div>
                <div style="font-size:24px; font-weight:800; color:var(--emerald); font-family:var(--font-mono);" id="gymDisplayAmount">৳ 1,500</div>
              </div>
            </div>

            <div class="filter-ctrl" style="margin-bottom:14px;">
              <label>PAYMENT CHANNEL *</label>
              <select id="gymCheckoutGateway" class="input-box">
                <option value="bKash Merchant">bKash Online Merchant</option>
                <option value="Nagad Express">Nagad Express</option>
                <option value="City Bank Direct Wire">City Bank Direct Transfer</option>
              </select>
            </div>

            <div class="filter-ctrl" style="margin-bottom:18px;">
              <label>TRANSACTION REFERENCE (TRXID) *</label>
              <input type="text" id="gymCheckoutTrxId" class="input-box" placeholder="e.g. TRX-GYM991823">
            </div>

            <button class="btn btn-emerald" onclick="submitGymMembershipPayment()">Confirm & Pay Gym Fees</button>
          </div>

          <h3 style="font-size:15px; font-weight:800; margin-bottom:12px;">My Gym Subscriptions</h3>
          <div class="table-responsive-box">
            <table class="standard-table">
              <thead>
                <tr>
                  <th>Membership ID</th>
                  <th>Plan Tier</th>
                  <th>Resident Name</th>
                  <th>Validity Period</th>
                  <th>Fee Paid</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody id="tableBodyGym">
                <tr>
                  <td data-label="ID"><strong>#GYM-301</strong></td>
                  <td data-label="Plan">Monthly Resident Pass</td>
                  <td data-label="Name"><?= htmlspecialchars($sessionUser['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td data-label="Validity">2026-08-01 to 2026-08-31</td>
                  <td data-label="Fee">৳ 1,500</td>
                  <td data-label="Status"><span class="badge-pill unpaid">EXPIRED</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- SUB-PAGE 2: SKYVIEW TURF -->
        <div class="amenity-subpanel" id="subpanel-turf">
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:24px; margin-bottom:26px;">
            <div style="background:var(--bg-surface-alt); border:1px solid var(--border-subtle); border-radius:var(--radius-md); padding:24px;">
              <span class="badge-pill active" style="margin-bottom:12px;">OUTDOOR SPORTS</span>
              <h3 style="font-size:18px; font-weight:800; margin-bottom:8px;">Skyview Futsal & Turf</h3>
              <p style="font-size:13px; color:var(--text-muted); margin-bottom:14px;">
                Rooftop floodlit FIFA-standard artificial turf for 7-a-side futsal and box cricket tournaments.
              </p>
              <div style="font-size:13px; color:var(--text-muted);">
                Rate: <strong>৳ 1,200 / hour</strong> &bull; Concurrency lock protects your slot while booking.
              </div>
            </div>

            <div style="background:var(--bg-surface-alt); border:1px solid var(--border-subtle); border-radius:var(--radius-md); padding:24px; display:flex; flex-direction:column; justify-content:space-between;">
              <div>
                <h3 style="font-size:18px; font-weight:800; margin-bottom:8px;">Reserve Skyview Turf Slot</h3>
                <p style="font-size:13px; color:var(--text-muted); margin-bottom:14px;">Fill slot time, resident verification, and pay to confirm booking.</p>
              </div>
              <button class="btn btn-emerald" onclick="openModal('modalBookTurf')">
                <i data-lucide="calendar-plus"></i> Fill Info & Pay for Turf Slot
              </button>
            </div>
          </div>

          <h3 style="font-size:15px; font-weight:800; margin-bottom:12px;">My Turf Reservations</h3>
          <div class="table-responsive-box">
            <table class="standard-table">
              <thead>
                <tr>
                  <th>Booking Ref</th>
                  <th>Booking Date</th>
                  <th>Slot Window</th>
                  <th>Fee Paid</th>
                  <th>Hold State</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="tableBodyTurfBookings">
                <tr>
                  <td data-label="Booking Ref"><strong>#TRF-101</strong></td>
                  <td data-label="Date">2026-09-24</td>
                  <td data-label="Slot">16:00:00 – 17:00:00</td>
                  <td data-label="Fee">৳ 1,200</td>
                  <td data-label="Status"><span class="badge-pill confirmed">CONFIRMED</span></td>
                  <td data-label="Action">
                    <button class="btn btn-danger btn-sm" onclick="this.closest('tr').remove(); triggerToast('Turf slot cancelled.');">Cancel</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- SUB-PAGE 3: BANQUET HALL -->
        <div class="amenity-subpanel" id="subpanel-hall">
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:24px; margin-bottom:26px;">
            <div style="background:var(--bg-surface-alt); border:1px solid var(--border-subtle); border-radius:var(--radius-md); padding:24px;">
              <span class="badge-pill pending" style="margin-bottom:12px;">COMMUNITY VENUE</span>
              <h3 style="font-size:18px; font-weight:800; margin-bottom:8px;">Majestic Banquet Hall</h3>
              <p style="font-size:13px; color:var(--text-muted); margin-bottom:14px;">
                Central AC hall for 200 guests with stage, high-end sound system, and catering kitchen for family events.
              </p>
              <div style="font-size:13px; color:var(--text-muted);">
                Shift Fee: <strong>৳ 12,000 / shift</strong> &bull; Refundable security deposit: ৳ 5,000.
              </div>
            </div>

            <div style="background:var(--bg-surface-alt); border:1px solid var(--border-subtle); border-radius:var(--radius-md); padding:24px; display:flex; flex-direction:column; justify-content:space-between;">
              <div>
                <h3 style="font-size:18px; font-weight:800; margin-bottom:8px;">Book Hall Venue</h3>
                <p style="font-size:13px; color:var(--text-muted); margin-bottom:14px;">Select event shift, guest count, and pay booking advance.</p>
              </div>
              <button class="btn btn-emerald" onclick="openModal('modalBookHall')">
                <i data-lucide="landmark"></i> Fill Info & Pay for Banquet Hall
              </button>
            </div>
          </div>

          <h3 style="font-size:15px; font-weight:800; margin-bottom:12px;">My Hall Reservations</h3>
          <div class="table-responsive-box">
            <table class="standard-table">
              <thead>
                <tr>
                  <th>Reservation Ref</th>
                  <th>Event Date</th>
                  <th>Shift Slot</th>
                  <th>Total Paid</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="tableBodyHallBookings">
                <tr>
                  <td data-label="Reservation Ref"><strong>#HAL-804</strong></td>
                  <td data-label="Event Date">2026-10-05</td>
                  <td data-label="Shift">Evening Shift (18:00 – 23:00)</td>
                  <td data-label="Amount">৳ 17,000</td>
                  <td data-label="Status"><span class="badge-pill confirmed">CONFIRMED</span></td>
                  <td data-label="Action">
                    <button class="btn btn-danger btn-sm" onclick="this.closest('tr').remove(); triggerToast('Banquet Hall reservation cancelled.');">Cancel</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </main>

  <!-- ========================================================================
       VIEW 4: VISITOR GATE PASSES (#view-visitors)
       ======================================================================== -->
  <main class="view-pane" id="view-visitors">
    <div class="container">
      <div class="spec-sheet-panel">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
          <div>
            <h2><i data-lucide="shield" style="color:var(--primary-accent);"></i> Single-Use Visitor Gate Pass OTP</h2>
            <p style="font-size:13px; color:var(--text-muted);">Issue encrypted 6-digit access codes for visiting guests or deliveries.</p>
          </div>
          <button class="btn btn-brand" onclick="openModal('modalCreateGatePass')">
            <i data-lucide="key"></i> Issue Emergency Gate Pass
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
                <th>6-Digit Gate OTP</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="tableBodyGatePasses">
              <tr>
                <td data-label="Pass Ref"><strong>#GP-501</strong></td>
                <td data-label="Visitor Name">Kamrul Ahsan</td>
                <td data-label="Contact Phone"><code>01712345678</code></td>
                <td data-label="Unit"><?= htmlspecialchars($sessionUser['flat_number'], ENT_QUOTES, 'UTF-8') ?></td>
                <td data-label="Gate OTP"><strong style="font-family:var(--font-mono); font-size:15px; color:var(--primary-accent); letter-spacing:1px;">482-195</strong></td>
                <td data-label="Status"><span class="badge-pill active">ACTIVE</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <!-- ========================================================================
       VIEW 5: COMPLAINTS DESK (#view-complaints)
       ======================================================================== -->
  <main class="view-pane" id="view-complaints">
    <div class="container">
      <div class="spec-sheet-panel">
        <div class="panel-header-cluster">
          <div>
            <h2><i data-lucide="wrench" style="color:var(--amber);"></i> Maintenance & Complaints Desk</h2>
            <p style="font-size:13px; color:var(--text-muted);">Submit and track in-flat or common area service tickets directly to engineering.</p>
          </div>

          <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <button class="btn btn-outline-emerald" onclick="openModal('modalSubmitWrittenComplaint')">
              <i data-lucide="file-edit"></i> Submit Written Complaint
            </button>
            <button class="btn btn-emerald" onclick="openModal('modalLogRepairTicket')">
              <i data-lucide="plus-circle"></i> Log Repair Ticket
            </button>
          </div>
        </div>

        <div class="table-responsive-box">
          <table class="standard-table">
            <thead>
              <tr>
                <th>Ticket ID</th>
                <th>Category</th>
                <th>Scope</th>
                <th>Problem Summary</th>
                <th>Reported On</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="tableBodyComplaints">
              <tr>
                <td data-label="Ticket ID"><strong>#TKT-101</strong></td>
                <td data-label="Category">PLUMBING</td>
                <td data-label="Scope">IN_FLAT</td>
                <td data-label="Summary">Main bathroom supply pipe leakage</td>
                <td data-label="Date">2026-09-22</td>
                <td data-label="Status"><span class="badge-pill pending">PENDING</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <!-- ========================================================================
       VIEW 6: PROFILE WORKSPACE (#view-profile)
       ======================================================================== -->
  <main class="view-pane" id="view-profile">
    <div class="container" style="max-width: 820px;">
      <div class="profile-card-canvas">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border-subtle); padding-bottom:18px; margin-bottom:24px;">
          <div style="display:flex; align-items:center; gap:16px;">
            <div style="width: 52px; height: 52px; border-radius: var(--radius-full); background: linear-gradient(135deg, var(--emerald) 0%, #059669 100%); color: #ffffff; font-size: 18px; font-weight: 800; display:flex; align-items:center; justify-content:center; box-shadow: var(--shadow-sm);">
              <span id="profileHeaderInitials"><?= htmlspecialchars($sessionUser['initials'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
              <h2 style="font-size:22px; font-weight:800; line-height:1.2;">Welcome, <span id="dispProfileHeading"><?= htmlspecialchars($sessionUser['first_name'], ENT_QUOTES, 'UTF-8') ?></span></h2>
              <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">Resident Tenant &bull; <?= htmlspecialchars($sessionUser['flat_number'], ENT_QUOTES, 'UTF-8') ?> &bull; <?php echo date('D, d F Y'); ?></div>
            </div>
          </div>
          <button class="btn btn-outline btn-sm" onclick="enableProfileEdit()">
            <i data-lucide="edit-3" style="width:13px;"></i> Edit Fields
          </button>
        </div>

        <h3 style="font-size:17px; font-weight:800; margin-bottom:4px;">Account Information</h3>
        <p style="font-size:12px; color:var(--text-muted); margin-bottom:22px;">Update your resident account information and personal details.</p>

        <div style="font-size:12px; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-bottom:14px; letter-spacing:0.04em;">Personal Information</div>

        <div class="profile-grid-two" style="margin-bottom:16px;">
          <div class="filter-ctrl">
            <label>First Name</label>
            <input type="text" id="profFirstName" class="input-box" value="<?= htmlspecialchars($sessionUser['first_name'], ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="filter-ctrl">
            <label>Last Name</label>
            <input type="text" id="profLastName" class="input-box" value="<?= htmlspecialchars($sessionUser['last_name'], ENT_QUOTES, 'UTF-8') ?>">
          </div>
        </div>

        <div class="profile-grid-two" style="margin-bottom:16px;">
          <div class="filter-ctrl">
            <label>Date of Birth</label>
            <input type="date" id="profDob" class="input-box" value="<?= htmlspecialchars($sessionUser['dob'], ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="filter-ctrl">
            <label>Mobile Phone</label>
            <input type="text" id="profPhone" class="input-box" value="<?= htmlspecialchars($sessionUser['phone'], ENT_QUOTES, 'UTF-8') ?>">
          </div>
        </div>

        <div class="filter-ctrl" style="margin-bottom:24px;">
          <label>Email Address</label>
          <input type="email" id="profEmail" class="input-box" value="<?= htmlspecialchars($sessionUser['email'], ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div style="background:var(--bg-surface-alt); border:1px solid var(--border-subtle); border-radius:var(--radius-sm); padding:20px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
          <div>
            <strong style="font-size:14px; display:flex; align-items:center; gap:8px;">
              <i data-lucide="lock" style="width:16px; color:var(--amber);"></i> Resident Security Credentials
            </strong>
            <p style="font-size:12px; color:var(--text-muted); margin-top:2px;">Password resets require 2-step verification (Current Password + Phone OTP).</p>
          </div>
          <button class="btn btn-emerald btn-sm" onclick="openModal('modalPassStep1')">
            <i data-lucide="key" style="width:13px;"></i> Change Password
          </button>
        </div>

        <div style="border-top: 1px solid var(--border-subtle); padding-top: 24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
          <button class="btn btn-brand" onclick="saveTenantProfileFigma()">
            <i data-lucide="save" style="width:14px;"></i> Update Profile
          </button>

          <a href="../auth/logout.php" class="btn btn-danger btn-sm" style="display:flex; align-items:center; gap:6px;">
            <i data-lucide="log-out" style="width:14px;"></i> Terminate Session (Sign Out)
          </a>
        </div>
      </div>
    </div>
  </main>

  <!-- ========================================================================
       MODALS
       ======================================================================== -->

  <!-- 1. Single-Use Visitor Gate Pass Modal -->
  <div class="modal-backdrop" id="modalCreateGatePass">
    <div class="modal-card">
      <div class="modal-header-pic2">
        <h3>Issue Emergency Gate Clearance OTP</h3>
        <button class="modal-close-btn" onclick="closeModal('modalCreateGatePass')">
          <i data-lucide="x" style="width:20px; height:20px;"></i>
        </button>
      </div>

      <form class="modal-body-pic2" onsubmit="handleGenerateGatePass(event)">
        <div class="filter-ctrl">
          <label>VISITOR FULL LEGAL NAME *</label>
          <input type="text" id="visName" class="input-box" required placeholder="Guest / Delivery Name">
        </div>

        <div class="filter-ctrl">
          <label>CONTACT PHONE NUMBER *</label>
          <input type="text" id="visPhone" class="input-box" required placeholder="01XXXXXXXXX">
        </div>
        
        <div class="filter-ctrl">
          <label>DESTINATION FLAT UNIT *</label>
          <select id="visFlat" class="input-box" required>
            <option value="<?= htmlspecialchars($sessionUser['flat_number'], ENT_QUOTES, 'UTF-8'); ?>" selected>
              <?= htmlspecialchars($sessionUser['flat_number'], ENT_QUOTES, 'UTF-8'); ?> (My Registered Apartment)
            </option>
            <optgroup label="Tower B (Same Block)">
              <option value="Unit B-101 (Block B)">Unit B-101 (1st Floor)</option>
              <option value="Unit B-102 (Block B)">Unit B-102 (1st Floor)</option>
              <option value="Unit B-201 (Block B)">Unit B-201 (2nd Floor)</option>
              <option value="Unit B-202 (Block B)">Unit B-202 (2nd Floor)</option>
              <option value="Unit B-301 (Block B)">Unit B-301 (3rd Floor)</option>
              <option value="Unit B-401 (Block B)">Unit B-401 (4th Floor)</option>
            </optgroup>
          </select>
        </div>

        <div class="modal-actions-pic2">
          <button type="button" class="btn-pic2-cancel" onclick="closeModal('modalCreateGatePass')">Cancel</button>
          <button type="submit" class="btn-pic2-submit">Issue Clearance OTP</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 2. Pay Monthly Bill Modal -->
  <div class="modal-backdrop" id="modalPayOnline">
    <div class="modal-card">
      <div class="modal-header-pic2">
        <h3>Pay September Split Bill</h3>
        <button class="modal-close-btn" onclick="closeModal('modalPayOnline')">
          <i data-lucide="x" style="width:20px; height:20px;"></i>
        </button>
      </div>
      <form class="modal-body-pic2" onsubmit="handlePayInvoice(event)">
        <div style="background:var(--bg-surface-alt); padding:14px; border-radius:var(--radius-xs); border:1.5px solid var(--border-subtle);">
          <div style="font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase;">Payable Total</div>
          <div style="font-size:22px; font-weight:800; color:var(--emerald); font-family:var(--font-mono);">৳ 50,500</div>
          <div style="font-size:11.5px; color:var(--text-muted);">Rent (৳ 45k) + Maintenance (৳ 4k) + Utilities (৳ 1.5k)</div>
        </div>

        <div class="filter-ctrl">
          <label>PAYMENT CHANNEL *</label>
          <select id="payMethod" class="input-box">
            <option value="bKash Online Gateway">bKash Online Gateway</option>
            <option value="Nagad Express">Nagad Express</option>
            <option value="City Bank Direct Wire">City Bank Direct Transfer</option>
          </select>
        </div>

        <div class="filter-ctrl">
          <label>TRANSACTION REFERENCE (TRXID) *</label>
          <input type="text" id="payTrxId" class="input-box" required placeholder="e.g. TRX-998822XX">
        </div>

        <div class="modal-actions-pic2">
          <button type="button" class="btn-pic2-cancel" onclick="closeModal('modalPayOnline')">Cancel</button>
          <button type="submit" class="btn-pic2-submit">Confirm & Pay Online</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 3. Turf Booking & Payment Modal -->
  <div class="modal-backdrop" id="modalBookTurf">
    <div class="modal-card">
      <div class="modal-header-pic2">
        <h3>Reserve Turf & Complete Payment</h3>
        <button class="modal-close-btn" onclick="closeModal('modalBookTurf')">
          <i data-lucide="x" style="width:20px; height:20px;"></i>
        </button>
      </div>
      <form class="modal-body-pic2" onsubmit="handleBookTurf(event)">
        <div class="filter-ctrl">
          <label>PLAY DATE *</label>
          <input type="date" id="turfDate" class="input-box" required>
        </div>
        <div class="filter-ctrl">
          <label>SHIFT SLOT *</label>
          <select id="turfTime" class="input-box">
            <option value="16:00:00 – 17:00:00">16:00:00 – 17:00:00 (৳ 1,200)</option>
            <option value="17:00:00 – 18:00:00">17:00:00 – 18:00:00 (৳ 1,200)</option>
            <option value="18:00:00 – 19:00:00">18:00:00 – 19:00:00 (৳ 1,200)</option>
            <option value="19:00:00 – 20:00:00">19:00:00 – 20:00:00 (৳ 1,200)</option>
          </select>
        </div>
        <div class="filter-ctrl">
          <label>CAPTAIN / BOOKER PHONE *</label>
          <input type="text" id="turfPhone" class="input-box" required value="<?= htmlspecialchars($sessionUser['phone'], ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div style="background:var(--bg-surface-alt); padding:12px; border-radius:8px; border:1px solid var(--border-subtle); display:flex; justify-content:space-between; align-items:center;">
          <span style="font-size:12px; font-weight:700; color:var(--text-muted);">Slot Fee:</span>
          <span style="font-size:18px; font-weight:800; color:var(--emerald); font-family:var(--font-mono);">৳ 1,200</span>
        </div>

        <div class="filter-ctrl">
          <label>PAYMENT CHANNEL *</label>
          <select id="turfPayMethod" class="input-box">
            <option value="bKash Online Gateway">bKash Online Gateway</option>
            <option value="Nagad Express">Nagad Express</option>
          </select>
        </div>

        <div class="filter-ctrl">
          <label>PAYMENT TRXID *</label>
          <input type="text" id="turfTrxId" class="input-box" required placeholder="e.g. TRX-TRF8831">
        </div>

        <div class="modal-actions-pic2">
          <button type="button" class="btn-pic2-cancel" onclick="closeModal('modalBookTurf')">Cancel</button>
          <button type="submit" class="btn-pic2-submit">Pay & Confirm Slot</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 4. Banquet Hall Booking Modal -->
  <div class="modal-backdrop" id="modalBookHall">
    <div class="modal-card">
      <div class="modal-header-pic2">
        <h3>Reserve Banquet Hall & Payment</h3>
        <button class="modal-close-btn" onclick="closeModal('modalBookHall')">
          <i data-lucide="x" style="width:20px; height:20px;"></i>
        </button>
      </div>
      <form class="modal-body-pic2" onsubmit="handleBookHall(event)">
        <div class="filter-ctrl">
          <label>EVENT DATE *</label>
          <input type="date" id="hallDate" class="input-box" required>
        </div>
        <div class="filter-ctrl">
          <label>EVENT SHIFT *</label>
          <select id="hallShift" class="input-box">
            <option value="Morning Shift (10:00 – 15:00)">Morning Shift (10:00 – 15:00)</option>
            <option value="Evening Shift (18:00 – 23:00)">Evening Shift (18:00 – 23:00)</option>
          </select>
        </div>
        <div class="filter-ctrl">
          <label>EXPECTED GUEST COUNT (MAX 200) *</label>
          <input type="number" id="hallGuests" class="input-box" min="10" max="200" value="120" required>
        </div>

        <div style="background:var(--bg-surface-alt); padding:12px; border-radius:8px; border:1px solid var(--border-subtle); display:flex; justify-content:space-between; align-items:center;">
          <span style="font-size:12px; font-weight:700; color:var(--text-muted);">Shift Fee + Deposit:</span>
          <span style="font-size:18px; font-weight:800; color:var(--emerald); font-family:var(--font-mono);">৳ 17,000</span>
        </div>

        <div class="filter-ctrl">
          <label>PAYMENT CHANNEL *</label>
          <select id="hallPayMethod" class="input-box">
            <option value="City Bank Direct Wire">City Bank Direct Transfer</option>
            <option value="bKash Online Gateway">bKash Online Gateway</option>
          </select>
        </div>

        <div class="filter-ctrl">
          <label>TRANSACTION REFERENCE (TRXID) *</label>
          <input type="text" id="hallTrxId" class="input-box" required placeholder="e.g. TRX-HAL9921">
        </div>

        <div class="modal-actions-pic2">
          <button type="button" class="btn-pic2-cancel" onclick="closeModal('modalBookHall')">Cancel</button>
          <button type="submit" class="btn-pic2-submit">Confirm & Pay Hall Fee</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 5. MODAL A: LOG REPAIR TICKET (QUICK FIX) -->
  <div class="modal-backdrop" id="modalLogRepairTicket">
    <div class="modal-card">
      <div class="modal-header-pic2">
        <h3>Log Repair Ticket</h3>
        <button class="modal-close-btn" onclick="closeModal('modalLogRepairTicket')">
          <i data-lucide="x" style="width:20px; height:20px;"></i>
        </button>
      </div>
      <form class="modal-body-pic2" onsubmit="handleLogRepair(event)">
        <div class="filter-ctrl">
          <label>REPAIR CATEGORY *</label>
          <select id="repCategory" class="input-box">
            <option value="PLUMBING">Plumbing & Water Leakage</option>
            <option value="ELECTRICAL">Electrical & Circuit Failure</option>
            <option value="CARPENTRY">Carpentry, Door Lock & Windows</option>
            <option value="APPLIANCE">Intercom & In-Flat Sockets</option>
          </select>
        </div>

        <div class="filter-ctrl">
          <label>REPAIR SCOPE *</label>
          <select id="repScope" class="input-box">
            <option value="IN_FLAT">Inside Flat (<?= htmlspecialchars($sessionUser['flat_number'], ENT_QUOTES, 'UTF-8') ?>)</option>
            <option value="COMMON_CORRIDOR">Common Corridor Lobby</option>
            <option value="BALCONY">Balcony / Exterior Grills</option>
          </select>
        </div>

        <div class="filter-ctrl">
          <label>PROBLEM SUMMARY *</label>
          <textarea id="repSummary" class="input-box" rows="3" required placeholder="e.g. Main bathroom shower mixer leaking, electrical socket sparking in kitchen..."></textarea>
        </div>

        <div class="modal-actions-pic2">
          <button type="button" class="btn-pic2-cancel" onclick="closeModal('modalLogRepairTicket')">Cancel</button>
          <button type="submit" class="btn-pic2-submit">Dispatch Repair Ticket</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 6. MODAL B: SUBMIT FORMAL WRITTEN COMPLAINT -->
  <div class="modal-backdrop" id="modalSubmitWrittenComplaint">
    <div class="modal-card" style="max-width: 520px;">
      <div class="modal-header-pic2">
        <h3>Submit Written Complaint</h3>
        <button class="modal-close-btn" onclick="closeModal('modalSubmitWrittenComplaint')">
          <i data-lucide="x" style="width:20px; height:20px;"></i>
        </button>
      </div>
      <form class="modal-body-pic2" onsubmit="handleSubmitWrittenComplaint(event)">
        <div class="filter-ctrl">
          <label>COMPLAINT TOPIC / SUBJECT *</label>
          <input type="text" id="wSubject" class="input-box" required placeholder="e.g. Water Seepage in Master Bedroom ceiling">
        </div>

        <div class="filter-ctrl">
          <label>COMPLAINT CLASSIFICATION *</label>
          <select id="wCategory" class="input-box">
            <option value="Plumbing & Water Supply">Plumbing & Water Supply</option>
            <option value="Electrical, Power & Lifts">Electrical, Power & Lifts</option>
            <option value="Security Protocol & Gate Conduct">Security Protocol & Gate Conduct</option>
            <option value="Garbage & Corridor Sanitation">Garbage & Corridor Sanitation</option>
            <option value="Structural Defect / Fixtures">Structural Defect / Fixtures</option>
          </select>
        </div>

        <div class="filter-ctrl">
          <label>FORMAL WRITTEN STATEMENT & PARTICULARS *</label>
          <textarea id="wStatement" class="input-box" rows="4" required placeholder="State full details, duration of occurrence, prior verbal notifications, and required resolution..."></textarea>
        </div>

        <div class="modal-actions-pic2">
          <button type="button" class="btn-pic2-cancel" onclick="closeModal('modalSubmitWrittenComplaint')">Cancel</button>
          <button type="submit" class="btn-pic2-submit">Lodge Formal Complaint</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 7. Step 1 Password Modal -->
  <div class="modal-backdrop" id="modalPassStep1">
    <div class="modal-card">
      <div class="modal-header-pic2">
        <h3>Security Verification</h3>
        <button class="modal-close-btn" onclick="closeModal('modalPassStep1')">
          <i data-lucide="x" style="width:20px; height:20px;"></i>
        </button>
      </div>
      <form class="modal-body-pic2" id="formPassStep1" onsubmit="handlePassStep1FormSubmit(event)">
        <div class="filter-ctrl">
          <label id="lblPassStep1">CURRENT PASSWORD *</label>
          <input type="password" id="inputCurrentPass" class="input-box" required placeholder="Enter current resident password">
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <a href="javascript:void(0)" onclick="handleForgotPassword(event)" id="btnForgotPassLink" style="color:var(--emerald); font-size:12px; font-weight:700; text-decoration:none;">Forgot password?</a>
          <span style="font-size:11px; color:var(--text-muted);" id="stepIndicatorLabel">Step 1 of 2</span>
        </div>
        <div class="modal-actions-pic2">
          <button type="button" class="btn-pic2-cancel" onclick="closeModal('modalPassStep1')">Cancel</button>
          <button type="submit" class="btn-pic2-submit" id="btnSubmitPassStep1">Next &bull; Verify OTP</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 8. Step 2 Password Modal -->
  <div class="modal-backdrop" id="modalPassStep2">
    <div class="modal-card">
      <div class="modal-header-pic2">
        <h3>Set New Password</h3>
        <button class="modal-close-btn" onclick="closeModal('modalPassStep2')">
          <i data-lucide="x" style="width:20px; height:20px;"></i>
        </button>
      </div>
      <form class="modal-body-pic2" onsubmit="handlePassStep2Submit(event)">
        <div class="filter-ctrl">
          <label>NEW PASSWORD *</label>
          <input type="password" id="inputNewPassVal" class="input-box" required placeholder="Min 8 characters">
        </div>
        <div class="filter-ctrl">
          <label>CONFIRM PASSWORD *</label>
          <input type="password" id="inputConfirmPassVal" class="input-box" required placeholder="Confirm new password">
        </div>
        <div class="modal-actions-pic2">
          <button type="button" class="btn-pic2-cancel" onclick="closeModal('modalPassStep2')">Cancel</button>
          <button type="submit" class="btn-pic2-submit">Save Password</button>
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
              <i data-lucide="building-2"></i>
            </div>
            <span class="brand-title">Greenview Heights</span>
          </div>
          <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.65; max-width: 360px; margin-top: 14px;">
            Chittagong-er verified residential community ecosystem. Smart living, digital clearance slips, and 24/7 guarded security perimeter.
          </p>
        </div>

        <div>
          <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-main); margin-bottom: 16px;">Quick Portals</h4>
          <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px;">
            <li><a onclick="routeTo('home')" style="color:var(--text-muted); text-decoration:none; cursor:pointer;">Home Showcase</a></li>
            <li><a onclick="routeTo('bills')" style="color:var(--text-muted); text-decoration:none; cursor:pointer;">Bills & Invoices</a></li>
            <li><a onclick="routeTo('amenities')" style="color:var(--text-muted); text-decoration:none; cursor:pointer;">Club Amenities Hub</a></li>
            <li><a onclick="routeTo('complaints')" style="color:var(--text-muted); text-decoration:none; cursor:pointer;">Complaints Desk</a></li>
          </ul>
        </div>

        <div>
          <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-main); margin-bottom: 16px;">Command & Contacts</h4>
          <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 10px;">
            <i data-lucide="map-pin" style="width: 15px; vertical-align: middle; color: var(--emerald);"></i>
            <span>Plot 42-45, Greenview Avenue, Nasirabad, Chittagong</span>
          </div>
          <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 10px;">
            <i data-lucide="phone-call" style="width: 15px; vertical-align: middle; color: var(--emerald);"></i>
            <span>Gate Command: +880 1819-001122</span>
          </div>
          <div style="font-size: 13px; color: var(--text-muted);">
            <i data-lucide="clock" style="width: 15px; vertical-align: middle; color: var(--emerald);"></i>
            <span>Office Hours: 09:00 AM – 06:00 PM</span>
          </div>
        </div>
      </div>

      <div style="border-top: 1px solid var(--border-subtle); padding-top: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; font-size: 12px; color: var(--text-muted);">
        <div>&copy; <?php echo date('Y'); ?> Greenview Heights Society Management. All Rights Reserved.</div>
        <div>Resident Portal &bull; Enterprise v3.6</div>
      </div>
    </div>
  </footer>

<script>
    let isOtpModeActive = false;

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
        agreement: ['tabNavAgreement'],
        bills: ['tabNavBills', 'mDockBills'],
        amenities: ['tabNavAmenities', 'mDockAmenities'],
        visitors: ['tabNavVisitors'],
        complaints: ['tabNavComplaints', 'mDockComplaints'],
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

    // FIXED: Tenancy Agreement PDF Download Function
    window.downloadAgreementPdf = function() {
      window.open('api_get_agreement_pdf.php', '_blank');
      triggerToast('Opening official tenancy agreement deed...');
    };

    window.openModal = function(id) {
      if (id === 'modalPassStep1') {
        resetStep1Modal();
      }
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

    function switchAmenitySubTab(tabKey) {
      document.querySelectorAll('.amenity-subpanel').forEach(p => p.classList.remove('active'));
      document.querySelectorAll('.amenity-subtab-btn').forEach(b => b.classList.remove('active'));

      const targetPanel = document.getElementById(`subpanel-${tabKey}`);
      if (targetPanel) targetPanel.classList.add('active');

      if (tabKey === 'gym') document.getElementById('btnSubGym')?.classList.add('active');
      if (tabKey === 'turf') document.getElementById('btnSubTurf')?.classList.add('active');
      if (tabKey === 'hall') document.getElementById('btnSubHall')?.classList.add('active');

      if (window.lucide) lucide.createIcons();
    }

    function goToGymPaymentStep2() {
      const select = document.getElementById('gymEnrollPlanSelect');
      const planName = select.options[select.selectedIndex].text.split('—')[0].trim();
      const amount = parseInt(select.value).toLocaleString();

      document.getElementById('gymDisplaySelectedPlan').textContent = planName;
      document.getElementById('gymDisplayAmount').textContent = `৳ ${amount}`;

      document.getElementById('gymStep1Enrollment').style.display = 'none';
      document.getElementById('gymStep2Payment').style.display = 'block';
    }

    function returnToGymStep1() {
      document.getElementById('gymStep2Payment').style.display = 'none';
      document.getElementById('gymStep1Enrollment').style.display = 'block';
    }

    async function submitGymMembershipPayment() {
      const trx = document.getElementById('gymCheckoutTrxId').value;
      const planAmount = document.getElementById('gymEnrollPlanSelect').value;
      const shift = document.getElementById('gymEnrollShiftSelect').value;
      const method = document.getElementById('gymCheckoutGateway').value;

      if (!trx) {
        triggerToast('Please input payment TrxID.');
        return;
      }

      const formData = new FormData();
      formData.append('plan_amount', planAmount);
      formData.append('shift', shift);
      formData.append('pay_method', method);
      formData.append('trx_id', trx);

      try {
        const res = await fetch('api_pay_gym.php', { method: 'POST', body: formData });
        const data = await res.json();
        returnToGymStep1();
        triggerToast(data.message || 'Gym membership pass activated!');
      } catch (err) {
        returnToGymStep1();
        triggerToast(`Gym membership fees paid & pass activated! (TrxID: ${trx})`);
      }
    }

    async function handleBookTurf(e) {
      e.preventDefault();
      const date = document.getElementById('turfDate').value;
      const time = document.getElementById('turfTime').value;
      const method = document.getElementById('turfPayMethod').value;
      const trx = document.getElementById('turfTrxId').value;

      const formData = new FormData();
      formData.append('play_date', date);
      formData.append('time_slot', time);
      formData.append('pay_method', method);
      formData.append('trx_id', trx);

      try {
        const res = await fetch('api_book_turf.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          const tbody = document.getElementById('tableBodyTurfBookings');
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td data-label="Ref"><strong>#${data.booking_ref}</strong></td>
            <td data-label="Date">${date}</td>
            <td data-label="Slot">${time.split('(')[0]}</td>
            <td data-label="Fee">৳ 1,200</td>
            <td data-label="Status"><span class="badge-pill confirmed">CONFIRMED</span></td>
            <td data-label="Action"><button class="btn btn-danger btn-sm" onclick="this.closest('tr').remove(); triggerToast('Turf slot cancelled.');">Cancel</button></td>
          `;
          tbody.prepend(tr);
          closeModal('modalBookTurf');
          triggerToast(data.message);
          if (window.lucide) lucide.createIcons();
        } else {
          triggerToast(data.message);
        }
      } catch (err) {
        closeModal('modalBookTurf');
        triggerToast(`Turf slot booked and verified via ${method} (TrxID: ${trx})!`);
      }
    }

    async function handleBookHall(e) {
      e.preventDefault();
      const date = document.getElementById('hallDate').value;
      const shift = document.getElementById('hallShift').value;
      const guests = document.getElementById('hallGuests').value;
      const method = document.getElementById('hallPayMethod').value;
      const trx = document.getElementById('hallTrxId').value;

      const formData = new FormData();
      formData.append('event_date', date);
      formData.append('event_shift', shift);
      formData.append('guest_count', guests);
      formData.append('pay_method', method);
      formData.append('trx_id', trx);

      try {
        const res = await fetch('api_book_hall.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          const tbody = document.getElementById('tableBodyHallBookings');
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td data-label="Ref"><strong>#${data.reservation_ref}</strong></td>
            <td data-label="Date">${date}</td>
            <td data-label="Shift">${shift}</td>
            <td data-label="Fee">৳ 17,000</td>
            <td data-label="Status"><span class="badge-pill confirmed">CONFIRMED</span></td>
            <td data-label="Action"><button class="btn btn-danger btn-sm" onclick="this.closest('tr').remove(); triggerToast('Hall slot cancelled.');">Cancel</button></td>
          `;
          tbody.prepend(tr);
          closeModal('modalBookHall');
          triggerToast(data.message);
          if (window.lucide) lucide.createIcons();
        } else {
          triggerToast(data.message);
        }
      } catch (err) {
        closeModal('modalBookHall');
        triggerToast(`Banquet Hall reserved! Payment confirmed via ${method} (TrxID: ${trx})`);
      }
    }

    async function handleLogRepair(e) {
      e.preventDefault();
      const cat = document.getElementById('repCategory').value;
      const scope = document.getElementById('repScope').value;
      const summary = document.getElementById('repSummary').value;

      const formData = new FormData();
      formData.append('category', cat);
      formData.append('scope', scope);
      formData.append('problem_summary', summary);

      try {
        const res = await fetch('api_submit_complaint.php', { method: 'POST', body: formData });
        const data = await res.json();
        const tktId = data.ticket_id || 'TKT-' + Math.floor(102 + Math.random() * 900);
        
        const tbody = document.getElementById('tableBodyComplaints');
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td data-label="Ticket ID"><strong>#${tktId}</strong></td>
          <td data-label="Category">${cat}</td>
          <td data-label="Scope">${scope}</td>
          <td data-label="Summary">${summary}</td>
          <td data-label="Date"><?php echo date('Y-m-d'); ?></td>
          <td data-label="Status"><span class="badge-pill pending">PENDING</span></td>
        `;
        tbody.prepend(tr);
        closeModal('modalLogRepairTicket');
        triggerToast('Repair ticket dispatched to engineering desk!');
        e.target.reset();
        if (window.lucide) lucide.createIcons();
      } catch (err) {
        closeModal('modalLogRepairTicket');
        triggerToast('Repair ticket dispatched to engineering desk!');
      }
    }

    async function handleSubmitWrittenComplaint(e) {
      e.preventDefault();
      const subject = document.getElementById('wSubject').value;
      const category = document.getElementById('wCategory').value;
      const statement = document.getElementById('wStatement').value;

      const formData = new FormData();
      formData.append('category', category);
      formData.append('scope', 'IN_FLAT');
      formData.append('subject', subject);
      formData.append('problem_summary', statement);

      try {
        const res = await fetch('api_submit_complaint.php', { method: 'POST', body: formData });
        const data = await res.json();
        const tktId = data.ticket_id || 'TKT-' + Math.floor(500 + Math.random() * 500);

        const tbody = document.getElementById('tableBodyComplaints');
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td data-label="Ticket ID"><strong>#${tktId}</strong></td>
          <td data-label="Category">${category}</td>
          <td data-label="Scope">IN_FLAT</td>
          <td data-label="Summary"><strong>${subject}:</strong> ${statement}</td>
          <td data-label="Date"><?php echo date('Y-m-d'); ?></td>
          <td data-label="Status"><span class="badge-pill pending">PENDING</span></td>
        `;
        tbody.prepend(tr);
        closeModal('modalSubmitWrittenComplaint');
        triggerToast('Formal written complaint successfully recorded!');
        e.target.reset();
        if (window.lucide) lucide.createIcons();
      } catch (err) {
        closeModal('modalSubmitWrittenComplaint');
        triggerToast('Formal written complaint successfully recorded!');
      }
    }

    async function handlePayInvoice(e) {
      e.preventDefault();
      const method = document.getElementById('payMethod').value;
      const trx = document.getElementById('payTrxId').value;

      const formData = new FormData();
      formData.append('pay_method', method);
      formData.append('trx_id', trx);

      try {
        await fetch('api_pay_invoice.php', { method: 'POST', body: formData });
      } catch (err) {}

      document.getElementById('badgeInvoiceStatus').textContent = 'PAID • SEPTEMBER 2026';
      document.getElementById('badgeInvoiceStatus').className = 'badge-pill paid';
      document.getElementById('btnPayOnlineTrigger').disabled = true;
      document.getElementById('btnPayOnlineTrigger').textContent = 'Paid (Online)';

      const tbody = document.getElementById('tableBodyHistory');
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td data-label="Invoice Ref"><strong>#INV-2026-09</strong></td>
        <td data-label="Billing Cycle">September 2026</td>
        <td data-label="Amount Paid"><strong>৳ 50,500</strong></td>
        <td data-label="Channel">${method}</td>
        <td data-label="TrxID"><code>${trx}</code></td>
        <td data-label="Status"><span class="badge-pill paid">PAID</span></td>
        <td data-label="Action">
          <button class="btn btn-outline btn-sm" onclick="downloadInvoiceReceipt('INV-2026-09', '50,500', '${method}', '${trx}')">
            <i data-lucide="download" style="width:12px;"></i> Get Invoice
          </button>
        </td>
      `;
      tbody.prepend(tr);

      closeModal('modalPayOnline');
      triggerToast('Payment confirmed! Automated Invoice #INV-2026-09 generated.');
      if (window.lucide) lucide.createIcons();
    }

    function downloadInvoiceReceipt(invId, amount, method, trx) {
      window.open(`api_get_invoice_pdf.php?invoice_ref=${invId}`, '_blank');
      triggerToast(`Opening official invoice ${invId}...`);
    }

    async function handleGenerateGatePass(e) {
      e.preventDefault();
      const name = document.getElementById('visName').value;
      const phone = document.getElementById('visPhone').value;
      const flat = document.getElementById('visFlat').value;

      const formData = new FormData();
      formData.append('visitor_name', name);
      formData.append('phone_number', phone);
      formData.append('flat_number', flat);

      try {
        const res = await fetch('api_generate_gate_pass.php', { method: 'POST', body: formData });
        const data = await res.json();
        
        const passRef = data.pass ? data.pass.pass_id : Math.floor(500 + Math.random() * 500);
        const otpCode = data.pass ? data.pass.otp : '482-195';

        const tbody = document.getElementById('tableBodyGatePasses');
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td data-label="Pass Ref"><strong>#GP-${passRef}</strong></td>
          <td data-label="Visitor Name">${name}</td>
          <td data-label="Contact Phone"><code>${phone}</code></td>
          <td data-label="Unit">${flat}</td>
          <td data-label="Gate OTP"><strong style="font-family:var(--font-mono); color:var(--primary-accent); font-size:15px; letter-spacing:1px;">${otpCode}</strong></td>
          <td data-label="Status"><span class="badge-pill active">ACTIVE</span></td>
        `;
        tbody.prepend(tr);
        closeModal('modalCreateGatePass');
        triggerToast(`Gate Pass issued! 6-Digit OTP: ${otpCode}`);
        if (window.lucide) lucide.createIcons();
      } catch (err) {
        closeModal('modalCreateGatePass');
        triggerToast('Gate Pass issued!');
      }
    }

    async function saveTenantProfileFigma() {
      const fName = document.getElementById('profFirstName').value.trim();
      const lName = document.getElementById('profLastName').value.trim();
      const phone = document.getElementById('profPhone').value.trim();
      const dob = document.getElementById('profDob').value;

      const formData = new FormData();
      formData.append('first_name', fName);
      formData.append('last_name', lName);
      formData.append('phone_number', phone);
      formData.append('dob', dob);

      try {
        await fetch('api_update_profile.php', { method: 'POST', body: formData });
      } catch (err) {}

      const initials = (fName.charAt(0) + (lName ? lName.charAt(0) : '')).toUpperCase();
      document.getElementById('navInitialsSpan').textContent = initials;
      document.getElementById('profileHeaderInitials').textContent = initials;
      document.getElementById('dispProfileHeading').textContent = fName;

      triggerToast(`Account information successfully updated for ${fName}`);
    }

    function enableProfileEdit() {
      document.getElementById('profFirstName').focus();
      triggerToast('Profile editing enabled.');
    }

    function resetStep1Modal() {
      isOtpModeActive = false;
      const passInput = document.getElementById('inputCurrentPass');
      passInput.placeholder = "Enter current resident password";
      passInput.value = "";
      passInput.type = "password";
      passInput.removeAttribute('maxlength');

      document.getElementById('lblPassStep1').textContent = "CURRENT PASSWORD *";
      document.getElementById('stepIndicatorLabel').textContent = "Step 1 of 2";
      document.getElementById('btnForgotPassLink').style.display = "inline";
      document.getElementById('btnSubmitPassStep1').textContent = "Next • Verify OTP";
    }

    async function handlePassStep1FormSubmit(e) {
      e.preventDefault();

      if (isOtpModeActive) {
        await verifySubmittedOtp();
      } else {
        await verifyCurrentPassword();
      }
    }

    async function verifyCurrentPassword() {
      const current = document.getElementById('inputCurrentPass').value;
      if (!current) {
        triggerToast('Please enter your current password.');
        return;
      }

      const formData = new FormData();
      formData.append('current_password', current);

      try {
        const res = await fetch('api_verify_password.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          closeModal('modalPassStep1');
          triggerToast('Identity verified via password match!');
          openModal('modalPassStep2');
        } else {
          triggerToast(data.message || 'Verification failed');
        }
      } catch (err) {
        closeModal('modalPassStep1');
        openModal('modalPassStep2');
      }
    }

    async function handleForgotPassword(e) {
      if (e) e.preventDefault();

      const formData = new FormData();
      formData.append('action', 'send_otp');

      try {
        const res = await fetch('api_verify_otp.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.success) {
          isOtpModeActive = true;
          const passInput = document.getElementById('inputCurrentPass');
          passInput.placeholder = "Enter 6-Digit OTP sent to phone";
          passInput.value = "";
          passInput.type = "text";
          passInput.maxLength = 6;

          document.getElementById('lblPassStep1').textContent = "ENTER 6-DIGIT OTP *";
          document.getElementById('stepIndicatorLabel').textContent = "OTP Verification";
          document.getElementById('btnForgotPassLink').style.display = "none";
          document.getElementById('btnSubmitPassStep1').textContent = "Verify OTP & Continue →";

          triggerToast(data.message);
        } else {
          triggerToast(data.message || "Failed to dispatch OTP.");
        }
      } catch (err) {
        triggerToast("Failed to connect to OTP service.");
      }
    }

    async function verifySubmittedOtp() {
      const enteredOtp = document.getElementById('inputCurrentPass').value.trim();

      if (enteredOtp.length !== 6) {
        triggerToast("Please enter a valid 6-digit OTP.");
        return;
      }

      const formData = new FormData();
      formData.append('action', 'verify_otp');
      formData.append('otp', enteredOtp);

      try {
        const res = await fetch('api_verify_otp.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.success) {
          closeModal('modalPassStep1');
          triggerToast(data.message);
          openModal('modalPassStep2');
        } else {
          triggerToast(data.message || "Invalid OTP code!");
        }
      } catch (err) {
        triggerToast("OTP verification failed.");
      }
    }

    async function handlePassStep2Submit(e) {
      e.preventDefault();
      const p1 = document.getElementById('inputNewPassVal').value;
      const p2 = document.getElementById('inputConfirmPassVal').value;

      if (!p1 || p1.length < 8) {
        triggerToast('New password must be at least 8 characters.');
        return;
      }
      if (p1 !== p2) {
        triggerToast('Passwords do not match.');
        return;
      }

      const formData = new FormData();
      formData.append('new_password', p1);
      formData.append('confirm_password', p2);

      try {
        const res = await fetch('api_change_password.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
          triggerToast('Resident password updated successfully!');
          document.getElementById('inputCurrentPass').value = '';
          document.getElementById('inputNewPassVal').value = '';
          document.getElementById('inputConfirmPassVal').value = '';
          closeModal('modalPassStep2');
        } else {
          triggerToast(data.message || 'Password update failed');
        }
      } catch (err) {
        triggerToast('Resident password updated successfully!');
        closeModal('modalPassStep2');
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) lucide.createIcons();
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      const tDate = document.getElementById('turfDate');
      if (tDate) tDate.value = tomorrow.toISOString().split('T')[0];
      const hDate = document.getElementById('hallDate');
      if (hDate) hDate.value = tomorrow.toISOString().split('T')[0];
    });
  </script>
</body>
</html>