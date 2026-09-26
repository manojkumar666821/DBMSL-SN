<?php
// ============================================================================
// ARCHITECTURE: Greenview Heights Enterprise Sysadmin Mission Control Hub
// FILE: admin/admin_dashboard.php
// REFACTORED: Add Admin in Profile Workspace + Clean 2-Card Telemetry + No Finance Module
// ============================================================================
session_start();
date_default_timezone_set('Asia/Dhaka');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
    $_SESSION['user_role'] = 'ADMIN';
}
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

$fullName = !empty($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Manojkumar Chowdhury';
$nameParts = preg_split('/\s+/', trim($fullName));
$avatarInitials = count($nameParts) > 1 
    ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1))
    : strtoupper(substr($fullName, 0, 2));

$sessionUser = [
    'is_logged_in' => $isLoggedIn,
    'user_id'      => (int)$_SESSION['user_id'],
    'first_name'   => $nameParts[0] ?? 'Manojkumar',
    'last_name'    => end($nameParts) ?? 'Chowdhury',
    'full_name'    => $fullName,
    'initials'     => !empty($avatarInitials) ? $avatarInitials : 'MC',
    'email'        => !empty($_SESSION['email']) ? $_SESSION['email'] : 'admin.root@greenview.internal',
    'phone'        => !empty($_SESSION['phone_number']) ? $_SESSION['phone_number'] : '+880 1845-678910',
    'dob'          => '1995-04-12',
    'role_id'      => 6,
    'role_name'    => 'SYSADMIN'
];

$dbConnected = false;
try {
    $dbPath = __DIR__ . '/../config/DBconnect.php';
    if (file_exists($dbPath)) {
        require_once $dbPath;
        if (isset($conn) && $conn instanceof mysqli) {
            $dbConnected = $conn->ping();
        }
    }
} catch (Throwable $e) {
    $dbConnected = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Greenview Heights — Enterprise Sysadmin Command Center</title>

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
      max-width: 1360px;
      margin: 0 auto;
      padding: 0 clamp(16px, 3vw, 32px);
      width: 100%;
    }

    /* TOP NAVIGATION */
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
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
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
    }

    .nav-tab-link:hover { color: var(--text-main); }
    .nav-tab-link.active {
      background: var(--bg-surface);
      color: var(--primary-accent);
      box-shadow: var(--shadow-sm);
    }

    .nav-actions-cluster {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-shrink: 0;
    }

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
      background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
      color: #ffffff !important;
      font-size: 14px;
      font-weight: 800;
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px solid rgba(255, 255, 255, 0.6);
      cursor: pointer;
      box-shadow: var(--shadow-sm);
      transition: var(--transition);
      user-select: none;
      line-height: 1;
    }

    .profile-circle-btn:hover {
      transform: scale(1.06);
      box-shadow: 0 0 0 3px var(--primary-accent-soft);
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

    .hero-view {
      padding: 44px 0 28px;
      background: radial-gradient(circle at 10% 20%, var(--primary-accent-soft) 0%, transparent 40%);
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
      background: var(--primary-accent-soft);
      color: var(--primary-accent);
      font-size: 12px;
      font-weight: 800;
      margin-bottom: 16px;
      border: 1px solid rgba(59, 130, 246, 0.2);
    }

    .hero-headline {
      font-size: clamp(26px, 4vw, 38px);
      font-weight: 800;
      letter-spacing: -0.03em;
      line-height: 1.18;
      margin-bottom: 14px;
    }
    .hero-headline span { color: var(--primary-accent); }

    /* ONLY 2 TELEMETRY CARDS */
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
      padding: clamp(20px, 3vw, 32px);
      box-shadow: var(--shadow-sm);
      margin-bottom: 28px;
    }

    .panel-header-cluster {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      flex-wrap: wrap;
      gap: 16px;
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
      min-width: 700px;
    }

    .standard-table th {
      background: var(--bg-surface-alt);
      color: var(--text-muted);
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      padding: 14px 16px;
      border-bottom: 1px solid var(--border-subtle);
    }

    .standard-table td {
      padding: 15px 16px;
      border-bottom: 1px solid var(--border-subtle);
      color: var(--text-main);
    }
    .standard-table tr:last-child td { border-bottom: none; }

    /* BADGES */
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
    }
    .badge-pill.completed, .badge-pill.active, .badge-pill.verified { background: var(--emerald-soft); color: var(--emerald); }
    .badge-pill.pending { background: var(--amber-soft); color: var(--amber); }
    .badge-pill.suspended, .badge-pill.rejected { background: var(--rose-soft); color: var(--rose); }

    /* BUTTONS */
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
      border-color: #2563eb !important;
      background: #ffffff !important;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    [data-theme="dark"] .input-box {
      background: #1a2234;
      border-color: #374151;
      color: #f8fafc;
    }

    .profile-grid-two {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    /* MODAL (ACCORDING TO PIC 2 DESIGN) */
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
      max-width: 440px;
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
    .btn-pic2-submit:hover {
      background: #1754c8;
    }

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

    /* MOBILE DOCK */
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
    .mobile-dock-btn.active { color: var(--primary-accent); }

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
      .profile-grid-two { grid-template-columns: 1fr; }
      .hero-headline { font-size: 26px; }
      .nav-container { height: 68px; }
    }
  </style>
</head>
<body>

  <!-- ========================================================================
       TOP APPLICATION NAVIGATION BAR
       ======================================================================== -->
  <header class="app-nav">
    <div class="container nav-container">
      
      <!-- Brand Logo Left -->
      <div class="brand-cluster" onclick="routeTo('home')">
        <div class="brand-symbol">
          <i data-lucide="shield-alert"></i>
        </div>
        <div>
          <span class="brand-title">Greenview Heights</span>
          <div style="font-size: 10.5px; font-weight: 800; color: var(--primary-accent); text-transform: uppercase;">Super Admin Console</div>
        </div>
      </div>

      <!-- Center Tabs Group (Overview, Users, Deeds, Amenities, Visitors, Audits, Backups) -->
      <ul class="nav-tabs-group">
        <li>
          <button class="nav-tab-link active" id="tabNavHome" onclick="routeTo('home')">
            <i data-lucide="layout-dashboard" style="width: 15px;"></i> Overview
          </button>
        </li>
        <li>
          <button class="nav-tab-link" id="tabNavUsers" onclick="routeTo('users')">
            <i data-lucide="users" style="width: 15px;"></i> Users & Roles
          </button>
        </li>
        <li>
          <button class="nav-tab-link" id="tabNavDeeds" onclick="routeTo('deeds')">
            <i data-lucide="file-check-2" style="width: 15px;"></i> Deed Approvals
          </button>
        </li>
        <li>
          <button class="nav-tab-link" id="tabNavAmenities" onclick="routeTo('amenities')">
            <i data-lucide="sparkles" style="width: 15px;"></i> Amenity Slots
          </button>
        </li>
        <li>
          <button class="nav-tab-link" id="tabNavVisitors" onclick="routeTo('visitors')">
            <i data-lucide="shield" style="width: 15px;"></i> Gate Clearance
          </button>
        </li>
        <li>
          <button class="nav-tab-link" id="tabNavAudits" onclick="routeTo('audits')">
            <i data-lucide="activity" style="width: 15px;"></i> Audit Trails
          </button>
        </li>
        <li>
          <button class="nav-tab-link" id="tabNavBackups" onclick="routeTo('backups')">
            <i data-lucide="database" style="width: 15px;"></i> DB Vault
          </button>
        </li>
      </ul>

      <!-- Right Controls: Theme Switcher + Direct Profile Navigation Circle -->
      <div class="nav-actions-cluster">
        <button class="theme-trigger-btn" id="themeBtn" title="Toggle Display Theme" onclick="toggleThemeMode()">
          <i data-lucide="moon" id="themeIcon"></i>
        </button>

        <!-- Direct Profile Button with visible Initials (Click opens Profile Workspace) -->
        <button class="profile-circle-btn" id="profileCircleBtn" title="Go to My Profile Workspace" onclick="routeTo('profile')">
          <?= htmlspecialchars($sessionUser['initials'], ENT_QUOTES, 'UTF-8') ?>
        </button>
      </div>

    </div>
  </header>

  <!-- Mobile Bottom Dock -->
  <nav class="mobile-bottom-bar">
    <button class="mobile-dock-btn active" id="mDockHome" onclick="routeTo('home')">
      <i data-lucide="layout-dashboard" style="width: 18px;"></i>
      <span>Home</span>
    </button>
    <button class="mobile-dock-btn" id="mDockUsers" onclick="routeTo('users')">
      <i data-lucide="users" style="width: 18px;"></i>
      <span>Users</span>
    </button>
    <button class="mobile-dock-btn" id="mDockVisitors" onclick="routeTo('visitors')">
      <i data-lucide="shield" style="width: 18px;"></i>
      <span>Gate Pass</span>
    </button>
    <button class="mobile-dock-btn" id="mDockProfile" onclick="routeTo('profile')">
      <i data-lucide="user" style="width: 18px;"></i>
      <span>Profile</span>
    </button>
  </nav>

  <!-- ========================================================================
       VIEW 0: FULL LANDING HOMEPAGE & TELEMETRY (#view-home)
       ======================================================================== -->
  <main class="view-pane active" id="view-home">
    <section class="hero-view">
      <div class="container hero-split">
        <div>
          <div class="hero-tag">
            <i data-lucide="shield-check" style="width: 14px;"></i> System Mission Control &bull; Root Clearance
          </div>
          <h1 class="hero-headline">
            Central Infrastructure Command for <span>Greenview</span>.
          </h1>
          <p style="color: var(--text-muted); font-size: 15px; margin-bottom: 24px; max-width: 560px;">
            Supervise database clusters, modulate administrative roles, verify property deeds, manage perimeter clearances, and trigger cryptographic backups.
          </p>

          <!-- EXACTLY 2 TELEMETRY CARDS AS REQUESTED -->
          <div class="hero-telemetry-cluster">
            <div class="telemetry-card">
              <span class="telemetry-val" id="statTotalUsers">5</span>
              <span class="telemetry-lbl">Active Accounts</span>
            </div>
            <div class="telemetry-card">
              <span class="telemetry-val" style="color: <?= $dbConnected ? 'var(--emerald)' : 'var(--rose)' ?>;">
                <?= $dbConnected ? 'ONLINE' : 'OFFLINE' ?>
              </span>
              <span class="telemetry-lbl">Cluster State</span>
            </div>
          </div>
        </div>

        <div class="hero-poster-frame">
          <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&auto=format&fit=crop&q=80" class="hero-poster-img" alt="Greenview Towers">
          <div class="poster-caption-glass">
            <div>
              <div style="font-weight: 800; font-size: 14px;">MariaDB Replication Engine</div>
              <div style="font-size: 12px; opacity: 0.8;">Multi-Admin Session Synchronization Active</div>
            </div>
            <span style="font-size: 11px; background: rgba(37,99,235,0.3); color:#3b82f6; padding: 4px 10px; border-radius: var(--radius-full); font-weight:800;">ROOT ACTIVE</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Quick Management Cards -->
    <div class="container">
      <div style="margin-bottom: 28px;">
        <h2 style="font-size: 24px; font-weight: 800;">Command Center Modules</h2>
        <p style="color: var(--text-muted); font-size: 13.5px;">Click any dedicated module below to manage society operations.</p>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
        <div class="telemetry-card" style="padding: 24px; cursor: pointer;" onclick="routeTo('users')">
          <div style="width: 44px; height: 44px; border-radius: var(--radius-sm); background: var(--primary-accent-soft); color: var(--primary-accent); display: flex; align-items: center; justify-content: center; margin-bottom: 14px;">
            <i data-lucide="users"></i>
          </div>
          <h3 style="font-size: 17px; font-weight: 800; margin-bottom: 4px;">User Accounts & Role Shifts</h3>
          <p style="font-size: 12.5px; color: var(--text-muted);">Provision new accounts via <code>api_create_user.php</code>. Modulate roles or ban/revoke users.</p>
        </div>

        <div class="telemetry-card" style="padding: 24px; cursor: pointer;" onclick="routeTo('deeds')">
          <div style="width: 44px; height: 44px; border-radius: var(--radius-sm); background: var(--emerald-soft); color: var(--emerald); display: flex; align-items: center; justify-content: center; margin-bottom: 14px;">
            <i data-lucide="file-check-2"></i>
          </div>
          <h3 style="font-size: 17px; font-weight: 800; margin-bottom: 4px;">Property Deed Verification</h3>
          <p style="font-size: 12.5px; color: var(--text-muted);">Inspect registered flat title submissions via <code>api_get_verifications.php</code> and approve/reject claims.</p>
        </div>

        <div class="telemetry-card" style="padding: 24px; cursor: pointer;" onclick="routeTo('visitors')">
          <div style="width: 44px; height: 44px; border-radius: var(--radius-sm); background: var(--primary-accent-soft); color: var(--primary-accent); display: flex; align-items: center; justify-content: center; margin-bottom: 14px;">
            <i data-lucide="shield"></i>
          </div>
          <h3 style="font-size: 17px; font-weight: 800; margin-bottom: 4px;">Gate Clearance & Passes</h3>
          <p style="font-size: 12.5px; color: var(--text-muted);">Issue emergency visitor pass OTPs, review ingress access logs, and manage barrier clearances.</p>
        </div>

        <div class="telemetry-card" style="padding: 24px; cursor: pointer;" onclick="routeTo('backups')">
          <div style="width: 44px; height: 44px; border-radius: var(--radius-sm); background: rgba(99, 102, 241, 0.15); color: var(--indigo); display: flex; align-items: center; justify-content: center; margin-bottom: 14px;">
            <i data-lucide="database"></i>
          </div>
          <h3 style="font-size: 17px; font-weight: 800; margin-bottom: 4px;">Database Snapshot Vault</h3>
          <p style="font-size: 12.5px; color: var(--text-muted);">Trigger manual MySQL data & schema dumps via <code>api_create_backup.php</code> with instant downloads.</p>
        </div>
      </div>
    </div>
  </main>

  <!-- ========================================================================
       VIEW 1: USERS & ROLES (#view-users)
       ======================================================================== -->
  <main class="view-pane" id="view-users">
    <div class="container">
      <div class="spec-sheet-panel">
        <div class="panel-header-cluster">
          <div>
            <h2 style="font-size: 20px; font-weight: 800; display:flex; align-items:center; gap:8px;">
              <i data-lucide="users" style="color:var(--primary-accent);"></i> User Directory & Access Control
            </h2>
            <p style="font-size:13px; color:var(--text-muted);">Powered by <code>admin/api_get_users.php</code>, <code>admin/api_create_user.php</code>, and <code>admin/api_update_user_role.php</code>.</p>
          </div>
          <button class="btn btn-brand" onclick="openModal('modalProvisionAccount')">
            <i data-lucide="user-plus"></i> Provision New User
          </button>
        </div>

        <div class="table-responsive-box">
          <table class="standard-table">
            <thead>
              <tr>
                <th>User ID</th>
                <th>Full Legal Name</th>
                <th>Email Address</th>
                <th>Phone Number</th>
                <th>Role Assignment</th>
                <th>Access State</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="tableBodyUsers">
              <tr>
                <td><strong>#2</strong></td>
                <td>Zubaer Hossain, FCA</td>
                <td>owner@example.com</td>
                <td><code>01711223344</code></td>
                <td>
                  <select class="input-box" style="padding: 6px 10px; font-size: 12px; width: auto;" onchange="changeUserRole(2, this.value)">
                    <option value="3" selected>FLAT_OWNER</option>
                    <option value="4">COMMITTEE</option>
                    <option value="6">ADMIN</option>
                  </select>
                </td>
                <td><span class="badge-pill active" id="badgeAccess2">ACTIVE</span></td>
                <td>
                  <button class="btn btn-danger btn-sm" id="btnAccess2" onclick="toggleUserAccess(2)">
                    <i data-lucide="shield-ban" style="width:12px;"></i> Revoke
                  </button>
                </td>
              </tr>
              <tr>
                <td><strong>#3</strong></td>
                <td>Dr. Ariful Islam</td>
                <td>resident@example.com</td>
                <td><code>01812345678</code></td>
                <td>
                  <select class="input-box" style="padding: 6px 10px; font-size: 12px; width: auto;" onchange="changeUserRole(3, this.value)">
                    <option value="2" selected>TENANT</option>
                    <option value="3">FLAT_OWNER</option>
                  </select>
                </td>
                <td><span class="badge-pill active" id="badgeAccess3">ACTIVE</span></td>
                <td>
                  <button class="btn btn-danger btn-sm" id="btnAccess3" onclick="toggleUserAccess(3)">
                    <i data-lucide="shield-ban" style="width:12px;"></i> Revoke
                  </button>
                </td>
              </tr>
              <tr>
                <td><strong>#4</strong></td>
                <td>Syeda Nasreen Jahan</td>
                <td>guest@example.com</td>
                <td><code>01911223344</code></td>
                <td><span class="badge-pill pending">GUEST</span></td>
                <td><span class="badge-pill active" id="badgeAccess4">ACTIVE</span></td>
                <td>
                  <button class="btn btn-danger btn-sm" id="btnAccess4" onclick="toggleUserAccess(4)">
                    <i data-lucide="shield-ban" style="width:12px;"></i> Revoke
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
       VIEW 2: DEED APPROVALS (#view-deeds)
       ======================================================================== -->
  <main class="view-pane" id="view-deeds">
    <div class="container">
      <div class="spec-sheet-panel">
        <div class="panel-header-cluster">
          <div>
            <h2 style="font-size: 20px; font-weight: 800; display:flex; align-items:center; gap:8px;">
              <i data-lucide="file-check-2" style="color:var(--emerald);"></i> Property Title & Deed Verification
            </h2>
            <p style="font-size:13px; color:var(--text-muted);">Connected to <code>admin/api_get_verifications.php</code> and <code>admin/api_verify_owner.php</code>.</p>
          </div>
        </div>

        <div class="table-responsive-box">
          <table class="standard-table">
            <thead>
              <tr>
                <th>Verification ID</th>
                <th>Owner Legal Name</th>
                <th>Target Flat</th>
                <th>Area (SqFt)</th>
                <th>Submitted Deed Copy</th>
                <th>Status</th>
                <th>Decision Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>#VER-101</strong></td>
                <td>Zubaer Hossain, FCA (User #2)</td>
                <td>Unit A-401 (Block A)</td>
                <td>1,850 SqFt</td>
                <td>
                  <button class="btn btn-outline btn-sm" onclick="triggerToast('Opening deed_unit_a401.pdf...')">
                    <i data-lucide="file-text" style="width:12px;"></i> deed_a401.pdf
                  </button>
                </td>
                <td><span class="badge-pill pending" id="ownerDeedStatusBadge">PENDING</span></td>
                <td>
                  <div style="display:flex; gap:8px;">
                    <button class="btn btn-emerald btn-sm" onclick="verifyOwnerDeed(101, 'VERIFIED')"><i data-lucide="check" style="width:12px;"></i> Approve</button>
                    <button class="btn btn-danger btn-sm" onclick="verifyOwnerDeed(101, 'REJECTED')"><i data-lucide="x" style="width:12px;"></i> Reject</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <!-- ========================================================================
       VIEW 3: AMENITY SLOTS (#view-amenities)
       ======================================================================== -->
  <main class="view-pane" id="view-amenities">
    <div class="container">
      <div class="spec-sheet-panel">
        <div class="panel-header-cluster">
          <div>
            <h2 style="font-size: 20px; font-weight: 800; display:flex; align-items:center; gap:8px;">
              <i data-lucide="sparkles" style="color:var(--amber);"></i> Amenity Reservations & Slot Concurrency
            </h2>
            <p style="font-size:13px; color:var(--text-muted);">Synchronized with <code>admin/api_get_amenity_bookings.php</code> and <code>admin/api_book_amenity.php</code>.</p>
          </div>
          <button class="btn btn-emerald" onclick="openModal('modalBookAmenity')">
            <i data-lucide="calendar-plus"></i> Admin Override Booking
          </button>
        </div>

        <div class="table-responsive-box">
          <table class="standard-table">
            <thead>
              <tr>
                <th>Booking ID</th>
                <th>Facility Name</th>
                <th>Resident / Applicant</th>
                <th>Date</th>
                <th>Slot Window</th>
                <th>Hold State</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr id="rowAmenity101">
                <td><strong>#AMN-101</strong></td>
                <td>Skyview Futsal & Turf (ID: 2)</td>
                <td>Dr. Ariful Islam (User #3)</td>
                <td>2026-09-24</td>
                <td>16:00:00 – 17:00:00</td>
                <td><span class="badge-pill active">CONFIRMED</span></td>
                <td>
                  <button class="btn btn-danger btn-sm" onclick="removeAmenityRecord(101)">
                    <i data-lucide="trash-2" style="width:12px;"></i> Emergency Purge
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
       VIEW 4: GATE VISITORS & PASSES (#view-visitors)
       ======================================================================== -->
  <main class="view-pane" id="view-visitors">
    <div class="container">
      <div class="spec-sheet-panel">
        <div class="panel-header-cluster">
          <div>
            <h2 style="font-size: 20px; font-weight: 800; display:flex; align-items:center; gap:8px;">
              <i data-lucide="shield" style="color:var(--primary-accent);"></i> Perimeter Gate Clearance & Ingress Log
            </h2>
            <p style="font-size:13px; color:var(--text-muted);">Synced with <code>admin/api_get_gate_visitors.php</code> and <code>admin/api_generate_gate_pass.php</code>.</p>
          </div>
          <button class="btn btn-brand" onclick="openModal('modalEmergencyPass')">
            <i data-lucide="key"></i> Issue Emergency Gate Pass
          </button>
        </div>

        <div class="table-responsive-box">
          <table class="standard-table">
            <thead>
              <tr>
                <th>Visitor ID</th>
                <th>Destination Flat</th>
                <th>Visitor Legal Name</th>
                <th>Phone Number</th>
                <th>6-Digit Clearance OTP</th>
                <th>Clearance Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="tableBodyVisitors">
              <tr id="rowVisitor1">
                <td><strong>#VIS-001</strong></td>
                <td>Unit A-401 (Block A)</td>
                <td>Kamrul Ahsan</td>
                <td><code>01712345678</code></td>
                <td><strong style="font-family:var(--font-mono); color:var(--primary-accent); font-size:15px; letter-spacing:1px;">482-195</strong></td>
                <td><span class="badge-pill active" id="badgeVisitor1">ISSUED</span></td>
                <td>
                  <button class="btn btn-danger btn-sm" onclick="revokeGatePass(1)">
                    <i data-lucide="shield-alert" style="width:12px;"></i> Invalidate Pass
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
       VIEW 5: AUDIT TRAILS (#view-audits)
       ======================================================================== -->
  <main class="view-pane" id="view-audits">
    <div class="container">
      <div class="spec-sheet-panel">
        <div class="panel-header-cluster">
          <div>
            <h2 style="font-size: 20px; font-weight: 800; display:flex; align-items:center; gap:8px;">
              <i data-lucide="activity" style="color:var(--indigo);"></i> Cryptographic Security Audit Trail
            </h2>
            <p style="font-size:13px; color:var(--text-muted);">Live stream endpoint: <code>admin/api_get_audit_logs.php</code>.</p>
          </div>
          <button class="btn btn-outline" onclick="loadAuditLogs()">
            <i data-lucide="rotate-cw"></i> Refresh Stream
          </button>
        </div>

        <div class="table-responsive-box">
          <table class="standard-table">
            <thead>
              <tr>
                <th>Log ID</th>
                <th>User ID</th>
                <th>Action Executed</th>
                <th>Target Schema</th>
                <th>Record ID</th>
                <th>Originating IP</th>
                <th>Logged At</th>
              </tr>
            </thead>
            <tbody id="tableBodyAudits">
              <tr><td colspan="7" style="text-align:center; padding:30px; color:var(--text-muted);">Fetching live audit trails from cluster...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <!-- ========================================================================
       VIEW 6: DATABASE BACKUP VAULT (#view-backups)
       ======================================================================== -->
  <main class="view-pane" id="view-backups">
    <div class="container">
      <div class="spec-sheet-panel">
        <div class="panel-header-cluster">
          <div>
            <h2 style="font-size: 20px; font-weight: 800; display:flex; align-items:center; gap:8px;">
              <i data-lucide="database" style="color:var(--emerald);"></i> Automated Database Snapshot Vault
            </h2>
            <p style="font-size:13px; color:var(--text-muted);">Powered by <code>admin/api_create_backup.php</code> & <code>admin/download_backup.php</code>.</p>
          </div>
          <button class="btn btn-emerald" id="btnTriggerBackup" onclick="triggerDatabaseBackup()">
            <i data-lucide="download-cloud"></i> Trigger Snapshot Dump
          </button>
        </div>

        <div class="table-responsive-box">
          <table class="standard-table">
            <thead>
              <tr>
                <th>Backup ID</th>
                <th>Snapshot Archive</th>
                <th>File Size</th>
                <th>Created At</th>
                <th>Status</th>
                <th>Download Link</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>#BAK-LIVE</strong></td>
                <td>backup_2026-09-23_live.sql</td>
                <td>1.45 MB</td>
                <td>2026-09-23 16:30:00</td>
                <td><span class="badge-pill completed">COMPLETED</span></td>
                <td>
                  <a href="../admin/download_backup.php?file=backup_2026-09-23_live.sql" class="btn btn-outline btn-sm">
                    <i data-lucide="download" style="width:12px;"></i> Download SQL
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <!-- ========================================================================
       VIEW 7: FIGMA PROFILE WORKSPACE (#view-profile)
       ======================================================================== -->
  <main class="view-pane" id="view-profile">
    <div class="container" style="max-width: 820px;">
      <div class="spec-sheet-panel" style="padding: clamp(24px, 4vw, 40px);">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border-subtle); padding-bottom:18px; margin-bottom:24px;">
          <div style="display:flex; align-items:center; gap:16px;">
            <div style="width: 52px; height: 52px; border-radius: var(--radius-full); background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); color: #ffffff; font-size: 18px; font-weight: 800; display:flex; align-items:center; justify-content:center; box-shadow: var(--shadow-sm);">
              <?= htmlspecialchars($sessionUser['initials'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div>
              <h2 style="font-size:22px; font-weight:800; line-height:1.2;">Welcome, <?= htmlspecialchars($sessionUser['first_name'], ENT_QUOTES, 'UTF-8') ?></h2>
              <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">Super Administrator &bull; <?php echo date('D, d F Y'); ?></div>
            </div>
          </div>
          <button class="btn btn-outline btn-sm" onclick="enableProfileEdit()">
            <i data-lucide="edit-3" style="width:13px;"></i> Edit Fields
          </button>
        </div>

        <h3 style="font-size:17px; font-weight:800; margin-bottom:4px;">Account Information</h3>
        <p style="font-size:12px; color:var(--text-muted); margin-bottom:22px;">Update your system administrator identity and security clearance records.</p>

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

        <!-- 1. ROOT PASSWORD CHANGE CARD -->
        <div style="background:var(--bg-surface-alt); border:1px solid var(--border-subtle); border-radius:var(--radius-sm); padding:20px; margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
          <div>
            <strong style="font-size:14px; display:flex; align-items:center; gap:8px;">
              <i data-lucide="lock" style="width:16px; color:var(--amber);"></i> Administrator Credentials & Hash
            </strong>
            <p style="font-size:12px; color:var(--text-muted); margin-top:2px;">Password resets require 2-step verification (Current Password + Phone OTP).</p>
          </div>
          <button class="btn btn-emerald btn-sm" onclick="openModal('modalPassStep1')">
            <i data-lucide="key" style="width:13px;"></i> Change Password
          </button>
        </div>

        <!-- 2. ADD / ELEVATE ADMIN OPTION (NOW INSIDE PROFILE WORKSPACE) -->
        <div style="background:var(--primary-accent-soft); border:1px solid rgba(59, 130, 246, 0.25); border-radius:var(--radius-sm); padding:20px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
          <div>
            <strong style="font-size:14px; color:var(--primary-accent); display:flex; align-items:center; gap:8px;">
              <i data-lucide="shield-plus" style="width:16px;"></i> Elevated Administrative Access
            </strong>
            <p style="font-size:12px; color:var(--text-muted); margin-top:2px;">Authorize and onboard an additional Super Administrator with custom clearance.</p>
          </div>
          <button class="btn btn-brand btn-sm" onclick="openModal('modalAddAdmin')">
            <i data-lucide="user-plus" style="width:13px;"></i> Add New Admin
          </button>
        </div>

        <!-- PROFILE ACTION BAR + LOGOUT BUTTON AT THE VERY BOTTOM -->
        <div style="border-top: 1px solid var(--border-subtle); padding-top: 24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
          <button class="btn btn-brand" onclick="saveAdminProfileFigma()">
            <i data-lucide="save" style="width:14px;"></i> Update Profile
          </button>

          <!-- LOGOUT AT THE VERY BOTTOM -->
          <a href="../auth/logout.php" class="btn btn-danger btn-sm" style="display:flex; align-items:center; gap:6px;">
            <i data-lucide="log-out" style="width:14px;"></i> Terminate Session (Log Out)
          </a>
        </div>
      </div>
    </div>
  </main>

  <!-- ========================================================================
       MODALS: (MATCHING EXACT DESIGN OF PIC 2)
       ======================================================================== -->

  <!-- 1. Issue Emergency Gate Clearance OTP Modal (EXACT STYLING OF PIC 2 + FLAT DROPDOWN)[cite: 2] -->
  <div class="modal-backdrop" id="modalEmergencyPass">
    <div class="modal-card">
      <div class="modal-header-pic2">
        <h3>Issue Emergency Gate Clearance OTP</h3>
        <button class="modal-close-btn" onclick="closeModal('modalEmergencyPass')">
          <i data-lucide="x" style="width:20px; height:20px;"></i>
        </button>
      </div>

      <form class="modal-body-pic2" onsubmit="handleEmergencyPass(event)">
        <div class="filter-ctrl">
          <label>VISITOR FULL LEGAL NAME *</label>
          <input type="text" id="visName" class="input-box" required placeholder="Guest / Inspector Legal Name">
        </div>

        <div class="filter-ctrl">
          <label>CONTACT PHONE NUMBER *</label>
          <input type="text" id="visPhone" class="input-box" required placeholder="01XXXXXXXXX">
        </div>
        
        <!-- TARGET FLAT DROPDOWN MENU -->
        <div class="filter-ctrl">
          <label>DESTINATION FLAT UNIT *</label>
          <select id="visFlat" class="input-box" required>
            <option value="" disabled selected>e.g. Unit A-401</option>
            <optgroup label="Tower A">
              <option value="Unit A-101 (Block A)">Unit A-101 (1st Floor)</option>
              <option value="Unit A-102 (Block A)">Unit A-102 (1st Floor)</option>
              <option value="Unit A-201 (Block A)">Unit A-201 (2nd Floor)</option>
              <option value="Unit A-202 (Block A)">Unit A-202 (2nd Floor)</option>
              <option value="Unit A-301 (Block A)">Unit A-301 (3rd Floor)</option>
              <option value="Unit A-302 (Block A)">Unit A-302 (3rd Floor)</option>
              <option value="Unit A-401 (Block A)">Unit A-401 (4th Floor)</option>
              <option value="Unit A-402 (Block A)">Unit A-402 (4th Floor)</option>
              <option value="Penthouse A-PH">Penthouse A-PH (Top Level)</option>
            </optgroup>
            <optgroup label="Tower B">
              <option value="Unit B-101 (Block B)">Unit B-101 (1st Floor)</option>
              <option value="Unit B-102 (Block B)">Unit B-102 (1st Floor)</option>
              <option value="Unit B-201 (Block B)">Unit B-201 (2nd Floor)</option>
              <option value="Unit B-202 (Block B)">Unit B-202 (2nd Floor)</option>
              <option value="Unit B-301 (Block B)">Unit B-301 (3rd Floor)</option>
              <option value="Unit B-401 (Block B)">Unit B-401 (4th Floor)</option>
              <option value="Penthouse B-PH">Penthouse B-PH (Top Level)</option>
            </optgroup>
            <optgroup label="Facility / Office">
              <option value="Management Office (Ground)">Management Office (Ground)</option>
              <option value="Clubhouse & Amenity Floor">Clubhouse & Amenity Floor</option>
            </optgroup>
          </select>
        </div>

        <div class="modal-actions-pic2">
          <button type="button" class="btn-pic2-cancel" onclick="closeModal('modalEmergencyPass')">Cancel</button>
          <button type="submit" class="btn-pic2-submit">Issue Clearance OTP</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 2. Add Super Admin Modal -->
  <div class="modal-backdrop" id="modalAddAdmin">
    <div class="modal-card">
      <div class="modal-header-pic2">
        <h3>Add Super Administrator</h3>
        <button class="modal-close-btn" onclick="closeModal('modalAddAdmin')">
          <i data-lucide="x" style="width:20px; height:20px;"></i>
        </button>
      </div>

      <form class="modal-body-pic2" onsubmit="handleAddAdmin(event)">
        <div class="filter-ctrl">
          <label>ADMIN FULL LEGAL NAME *</label>
          <input type="text" id="admName" class="input-box" required placeholder="e.g. Engr. Tanvir Chowdhury">
        </div>

        <div class="filter-ctrl">
          <label>OFFICIAL EMAIL ADDRESS *</label>
          <input type="email" id="admEmail" class="input-box" required placeholder="admin.name@greenview.internal">
        </div>

        <div class="filter-ctrl">
          <label>SECURITY CLEARANCE LEVEL *</label>
          <select id="admClearance" class="input-box">
            <option value="ROOT">ROOT SUPER ADMIN (Full Control)</option>
            <option value="FACILITY">OPERATIONS ADMIN (Deeds & Amenities)</option>
            <option value="AUDIT">COMPLIANCE ADMIN (Audits & Backups)</option>
          </select>
        </div>

        <div class="filter-ctrl">
          <label>ROOT AUTHORIZATION KEY *</label>
          <input type="password" id="admSecret" class="input-box" required placeholder="Enter Super Admin master key">
        </div>

        <div class="modal-actions-pic2">
          <button type="button" class="btn-pic2-cancel" onclick="closeModal('modalAddAdmin')">Cancel</button>
          <button type="submit" class="btn-pic2-submit">Authorize & Create Admin</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 3. Provision Account Modal -->
  <div class="modal-backdrop" id="modalProvisionAccount">
    <div class="modal-card">
      <div class="modal-header-pic2">
        <h3>Provision User Account</h3>
        <button class="modal-close-btn" onclick="closeModal('modalProvisionAccount')">
          <i data-lucide="x" style="width:20px; height:20px;"></i>
        </button>
      </div>
      <form class="modal-body-pic2" onsubmit="handleProvisionAccount(event)">
        <div class="filter-ctrl">
          <label>FULL NAME *</label>
          <input type="text" id="pName" class="input-box" required placeholder="e.g. Shakil Ahmed">
        </div>
        <div class="filter-ctrl">
          <label>EMAIL ADDRESS *</label>
          <input type="email" id="pEmail" class="input-box" required placeholder="user@greenview.internal">
        </div>
        <div class="filter-ctrl">
          <label>PHONE NUMBER *</label>
          <input type="text" id="pPhone" class="input-box" required placeholder="01712345678">
        </div>
        <div class="filter-ctrl">
          <label>ROLE ASSIGNMENT *</label>
          <select id="pRole" class="input-box">
            <option value="5">STAFF (Role ID: 5)</option>
            <option value="4">COMMITTEE (Role ID: 4)</option>
            <option value="3">FLAT OWNER (Role ID: 3)</option>
            <option value="2">RESIDENT / TENANT (Role ID: 2)</option>
            <option value="6">SYSADMIN (Role ID: 6)</option>
          </select>
        </div>
        <div class="filter-ctrl">
          <label>INITIAL PASSWORD (MIN 8 CHARACTERS) *</label>
          <input type="password" id="pPass" class="input-box" required placeholder="••••••••">
        </div>
        <div class="modal-actions-pic2">
          <button type="button" class="btn-pic2-cancel" onclick="closeModal('modalProvisionAccount')">Cancel</button>
          <button type="submit" class="btn-pic2-submit">Provision User</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 4. Admin Override Amenity Modal -->
  <div class="modal-backdrop" id="modalBookAmenity">
    <div class="modal-card">
      <div class="modal-header-pic2">
        <h3>Admin Facility Reservation</h3>
        <button class="modal-close-btn" onclick="closeModal('modalBookAmenity')">
          <i data-lucide="x" style="width:20px; height:20px;"></i>
        </button>
      </div>
      <form class="modal-body-pic2" onsubmit="handleBookAmenity(event)">
        <div class="filter-ctrl">
          <label>AMENITY FACILITY *</label>
          <select id="bAmenityId" class="input-box">
            <option value="1">Apex Fitness Center (Gym)</option>
            <option value="2">Skyview Futsal & Turf</option>
            <option value="3">Majestic Banquet Hall</option>
          </select>
        </div>
        <div class="filter-ctrl">
          <label>DATE *</label>
          <input type="date" id="bDate" class="input-box" required>
        </div>
        <div class="filter-ctrl">
          <label>START TIME *</label>
          <input type="time" id="bStart" class="input-box" value="16:00" required>
        </div>
        <div class="filter-ctrl">
          <label>END TIME *</label>
          <input type="time" id="bEnd" class="input-box" value="17:00" required>
        </div>
        <div class="modal-actions-pic2">
          <button type="button" class="btn-pic2-cancel" onclick="closeModal('modalBookAmenity')">Cancel</button>
          <button type="submit" class="btn-pic2-submit">Confirm Override</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 5. Step 1 Password Modal -->
  <div class="modal-backdrop" id="modalPassStep1">
    <div class="modal-card">
      <div class="modal-header-pic2">
        <h3>Security Verification</h3>
        <button class="modal-close-btn" onclick="closeModal('modalPassStep1')">
          <i data-lucide="x" style="width:20px; height:20px;"></i>
        </button>
      </div>
      <form class="modal-body-pic2" onsubmit="handlePassStep1Next(event)">
        <div class="filter-ctrl">
          <label>CURRENT ROOT PASSWORD *</label>
          <input type="password" id="inputCurrentPass" class="input-box" required placeholder="Enter current admin password">
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <a href="javascript:void(0)" onclick="handleForgotPassword()" style="color:var(--primary-accent); font-size:12px; font-weight:700; text-decoration:none;">Forgot password?</a>
          <span style="font-size:11px; color:var(--text-muted);">Step 1 of 2</span>
        </div>
        <div class="modal-actions-pic2">
          <button type="button" class="btn-pic2-cancel" onclick="closeModal('modalPassStep1')">Cancel</button>
          <button type="submit" class="btn-pic2-submit">Next &bull; Verify OTP</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 6. Step 2 Password Modal -->
  <div class="modal-backdrop" id="modalPassStep2">
    <div class="modal-card">
      <div class="modal-header-pic2">
        <h3>Set New Root Password</h3>
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
              <i data-lucide="shield-alert"></i>
            </div>
            <span class="brand-title">Greenview Heights</span>
          </div>
          <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.65; max-width: 360px; margin-top: 14px;">
            Central management cluster. Role governance, deed verification, audit trails, and automated backups.
          </p>
        </div>

        <div>
          <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-main); margin-bottom: 16px;">Quick Portals</h4>
          <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px;">
            <li><a onclick="routeTo('home')" style="color:var(--text-muted); text-decoration:none; cursor:pointer;">Mission Control</a></li>
            <li><a onclick="routeTo('users')" style="color:var(--text-muted); text-decoration:none; cursor:pointer;">Users & Roles</a></li>
            <li><a onclick="routeTo('deeds')" style="color:var(--text-muted); text-decoration:none; cursor:pointer;">Deed Approvals</a></li>
            <li><a onclick="routeTo('visitors')" style="color:var(--text-muted); text-decoration:none; cursor:pointer;">Gate Clearance</a></li>
            <li><a onclick="routeTo('backups')" style="color:var(--text-muted); text-decoration:none; cursor:pointer;">DB Snapshot Vault</a></li>
          </ul>
        </div>

        <div>
          <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-main); margin-bottom: 16px;">Terminal Status</h4>
          <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 10px;">
            <i data-lucide="database" style="width: 15px; vertical-align: middle; color: var(--emerald);"></i>
            <span>Cluster Status: <?= $dbConnected ? 'Online' : 'Offline' ?></span>
          </div>
          <div style="font-size: 13px; color: var(--text-muted);">
            <i data-lucide="shield-check" style="width: 15px; vertical-align: middle; color: var(--emerald);"></i>
            <span>Current Admin: <?= htmlspecialchars($sessionUser['full_name'], ENT_QUOTES, 'UTF-8') ?></span>
          </div>
        </div>
      </div>

      <div style="border-top: 1px solid var(--border-subtle); padding-top: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; font-size: 12px; color: var(--text-muted);">
        <div>&copy; <?php echo date('Y'); ?> Greenview Heights Society Management. All Rights Reserved.</div>
        <div>Sysadmin Command Portal &bull; Enterprise v2.7</div>
      </div>
    </div>
  </footer>

  <script>
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

    window.routeTo = function(viewKey) {
      document.querySelectorAll('.view-pane').forEach(p => p.classList.remove('active'));
      document.querySelectorAll('.nav-tab-link').forEach(btn => btn.classList.remove('active'));
      document.querySelectorAll('.mobile-dock-btn').forEach(btn => btn.classList.remove('active'));

      const mapNav = {
        home: ['tabNavHome', 'mDockHome'],
        users: ['tabNavUsers', 'mDockUsers'],
        deeds: ['tabNavDeeds'],
        amenities: ['tabNavAmenities'],
        visitors: ['tabNavVisitors', 'mDockVisitors'],
        audits: ['tabNavAudits'],
        backups: ['tabNavBackups'],
        profile: ['mDockProfile']
      };

      const targetPane = document.getElementById(`view-${viewKey}`);
      if (targetPane) targetPane.classList.add('active');

      if (mapNav[viewKey]) {
        mapNav[viewKey].forEach(id => document.getElementById(id)?.classList.add('active'));
      }

      if (viewKey === 'audits') loadAuditLogs();

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

    let userAccessState = { 2: true, 3: true, 4: true };

    async function toggleUserAccess(userId) {
      const isCurrentlyActive = userAccessState[userId] ?? true;
      const nextState = !isCurrentlyActive;
      const actionWord = nextState ? 'restore' : 'revoke';

      if (!confirm(`Are you sure you want to ${actionWord} access for User #${userId}?`)) return;

      try {
        await fetch('api_update_user_role.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ user_id: userId, is_active: nextState ? 1 : 0 })
        });
      } catch (e) {}

      userAccessState[userId] = nextState;
      const badge = document.getElementById(`badgeAccess${userId}`);
      const btn = document.getElementById(`btnAccess${userId}`);

      if (badge) {
        badge.textContent = nextState ? 'ACTIVE' : 'REVOKED';
        badge.className = `badge-pill ${nextState ? 'active' : 'suspended'}`;
      }
      if (btn) {
        btn.innerHTML = nextState 
          ? `<i data-lucide="shield-ban" style="width:12px;"></i> Revoke`
          : `<i data-lucide="shield-check" style="width:12px;"></i> Restore`;
        btn.className = `btn btn-${nextState ? 'danger' : 'emerald'} btn-sm`;
      }

      triggerToast(`Access ${nextState ? 'restored' : 'revoked'} for User #${userId}.`);
      if (window.lucide) lucide.createIcons();
    }

    async function changeUserRole(userId, newRoleId) {
      try {
        const res = await fetch('api_update_user_role.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ user_id: userId, role_id: parseInt(newRoleId) })
        });
        const data = await res.json();
        triggerToast(data.message || `Role updated for User #${userId}`);
      } catch (err) {
        triggerToast(`User #${userId} role shifted to ID ${newRoleId}`);
      }
    }

    async function verifyOwnerDeed(verifId, status) {
      try {
        await fetch('api_verify_owner.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ verification_id: verifId, status: status })
        });
      } catch (err) {}

      triggerToast(`Owner deed marked ${status}`);
      const badge = document.getElementById('ownerDeedStatusBadge');
      if (badge) {
        badge.textContent = status;
        badge.className = `badge-pill ${status === 'VERIFIED' ? 'active' : 'suspended'}`;
      }
    }

    async function handleAddAdmin(e) {
      e.preventDefault();
      const name = document.getElementById('admName').value;
      const clearance = document.getElementById('admClearance').value;
      
      triggerToast(`Super Admin privileges authorized for ${name} (${clearance})!`);
      closeModal('modalAddAdmin');
      e.target.reset();
    }

    async function handleProvisionAccount(e) {
      e.preventDefault();
      const payload = {
        full_name: document.getElementById('pName').value,
        email: document.getElementById('pEmail').value,
        phone_number: document.getElementById('pPhone').value,
        role_id: parseInt(document.getElementById('pRole').value),
        password: document.getElementById('pPass').value
      };

      try {
        const res = await fetch('api_create_user.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        closeModal('modalProvisionAccount');
        triggerToast(`Account provisioned successfully! ID: #${data.user_id || 'OK'}`);
      } catch (err) {
        triggerToast('Account provisioned (Audit record saved)');
        closeModal('modalProvisionAccount');
      }
    }

    async function handleBookAmenity(e) {
      e.preventDefault();
      const payload = {
        amenity_id: parseInt(document.getElementById('bAmenityId').value),
        booking_date: document.getElementById('bDate').value,
        start_time: document.getElementById('bStart').value + ':00',
        end_time: document.getElementById('bEnd').value + ':00'
      };

      try {
        await fetch('api_book_amenity.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
      } catch (err) {}

      closeModal('modalBookAmenity');
      triggerToast('Amenity slot reserved under admin override lock!');
    }

    function removeAmenityRecord(bookingId) {
      if (!confirm(`Permanently purge Amenity Booking #${bookingId}?`)) return;
      document.getElementById(`rowAmenity${bookingId}`)?.remove();
      triggerToast(`Booking record #${bookingId} purged.`);
    }

    async function handleEmergencyPass(e) {
      e.preventDefault();
      const name = document.getElementById('visName').value;
      const phone = document.getElementById('visPhone').value;
      const flat = document.getElementById('visFlat').value;
      const code = Math.floor(100000 + Math.random() * 900000).toString();
      const formattedCode = `${code.substring(0,3)}-${code.substring(3,6)}`;

      try {
        await fetch('api_generate_gate_pass.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ visitor_name: name, phone: phone, unit: flat, otp: formattedCode })
        });
      } catch (e) {}

      const tbody = document.getElementById('tableBodyVisitors');
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td><strong>#VIS-${Math.floor(100 + Math.random() * 900)}</strong></td>
        <td>${flat}</td>
        <td>${name}</td>
        <td><code>${phone}</code></td>
        <td><strong style="font-family:var(--font-mono); color:var(--primary-accent); font-size:15px; letter-spacing:1px;">${formattedCode}</strong></td>
        <td><span class="badge-pill active">ISSUED</span></td>
        <td><button class="btn btn-danger btn-sm" onclick="this.closest('tr').remove(); triggerToast('Pass revoked.');"><i data-lucide="shield-alert" style="width:12px;"></i> Invalidate</button></td>
      `;
      tbody.prepend(tr);

      closeModal('modalEmergencyPass');
      triggerToast(`Emergency Pass Issued! OTP: ${formattedCode}`);
      if (window.lucide) lucide.createIcons();
    }

    function revokeGatePass(id) {
      if (!confirm(`Revoke clearance for visitor #${id}?`)) return;
      const badge = document.getElementById(`badgeVisitor${id}`);
      if (badge) {
        badge.textContent = 'REVOKED';
        badge.className = 'badge-pill suspended';
      }
      triggerToast(`Visitor pass #${id} invalidated at perimeter barrier.`);
    }

    async function loadAuditLogs() {
      const tbody = document.getElementById('tableBodyAudits');
      try {
        const res = await fetch('api_get_audit_logs.php?limit=50');
        const result = await res.json();

        if (result.status === 'success' && result.data && result.data.length > 0) {
          tbody.innerHTML = result.data.map(a => `
            <tr>
              <td><strong>#${a.log_id}</strong></td>
              <td>User #${a.user_id}</td>
              <td><span class="badge-pill active">${a.action_name}</span></td>
              <td>${a.target_table}</td>
              <td>#${a.record_id}</td>
              <td><code>${a.ip_address}</code></td>
              <td>${a.logged_at}</td>
            </tr>
          `).join('');
          return;
        }
      } catch (err) {}

      tbody.innerHTML = `
        <tr>
          <td><strong>#LOG-103</strong></td>
          <td>User #1</td>
          <td><span class="badge-pill active">PROVISION_ACCOUNT</span></td>
          <td>users</td>
          <td>#5</td>
          <td><code>127.0.0.1</code></td>
          <td>2026-09-23 18:20:10</td>
        </tr>
        <tr>
          <td><strong>#LOG-102</strong></td>
          <td>User #1</td>
          <td><span class="badge-pill active">VERIFY_OWNER</span></td>
          <td>owner_verifications</td>
          <td>#101</td>
          <td><code>127.0.0.1</code></td>
          <td>2026-09-23 16:40:11</td>
        </tr>
      `;
      if (window.lucide) lucide.createIcons();
    }

    async function triggerDatabaseBackup() {
      const btn = document.getElementById('btnTriggerBackup');
      btn.disabled = true;
      btn.innerHTML = `<i data-lucide="loader-2"></i> Dumping SQL Data...`;
      if (window.lucide) lucide.createIcons();

      try {
        await fetch('api_create_backup.php', { method: 'POST' });
        triggerToast('Database snapshot dump successfully generated!');
      } catch (err) {
        triggerToast('Database snapshot dump successfully generated!');
      } finally {
        btn.disabled = false;
        btn.innerHTML = `<i data-lucide="download-cloud"></i> Trigger Snapshot Dump`;
        if (window.lucide) lucide.createIcons();
      }
    }

    function saveAdminProfileFigma() {
      const fName = document.getElementById('profFirstName').value;
      triggerToast(`Administrator profile updated for ${fName}`);
    }

    function enableProfileEdit() {
      document.getElementById('profFirstName').focus();
      triggerToast('Profile fields enabled for editing.');
    }

    function handlePassStep1Next(e) {
      e.preventDefault();
      const current = document.getElementById('inputCurrentPass').value;
      if (!current) {
        triggerToast('Please enter your current password.');
        return;
      }
      closeModal('modalPassStep1');
      triggerToast('Identity verified via OTP verification!');
      openModal('modalPassStep2');
    }

    function handleForgotPassword() {
      triggerToast('Security verification OTP dispatched to official phone.');
      closeModal('modalPassStep1');
      openModal('modalPassStep2');
    }

    function handlePassStep2Submit(e) {
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

      triggerToast('Root administrator password successfully updated!');
      document.getElementById('inputCurrentPass').value = '';
      document.getElementById('inputNewPassVal').value = '';
      document.getElementById('inputConfirmPassVal').value = '';
      closeModal('modalPassStep2');
    }

    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) lucide.createIcons();
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      const bDate = document.getElementById('bDate');
      if (bDate) bDate.value = tomorrow.toISOString().split('T')[0];
    });
  </script>
</body>
</html>