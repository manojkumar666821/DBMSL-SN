<?php
// ============================================================================
// ARCHITECTURE: Professional Enterprise Authentication Hub
// FILE: auth/login.php
// ============================================================================
session_start();

$mode = isset($_GET['mode']) ? trim($_GET['mode']) : 'login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Greenview Heights — Secure Authentication Hub</title>

  <!-- Typography & Lucide Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>

  <style>
    :root {
      --bg-canvas: #f1f5f9;
      --bg-surface: #ffffff;
      --bg-surface-alt: #f8fafc;
      --border-subtle: #e2e8f0;
      --border-strong: #cbd5e1;

      --text-main: #0f172a;
      --text-muted: #64748b;

      --primary-accent: #2563eb;
      --primary-accent-hover: #1d4ed8;
      --primary-accent-soft: #eff6ff;

      --radius-xs: 8px;
      --radius-sm: 12px;
      --radius-md: 16px;
      --radius-lg: 24px;
      --radius-full: 9999px;

      --shadow-card: 0 20px 40px -15px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(15, 23, 42, 0.05);
      --font-sans: 'Plus Jakarta Sans', sans-serif;
      --transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    [data-theme="dark"] {
      --bg-canvas: #090d16 !important;
      --bg-surface: #111827 !important;
      --bg-surface-alt: #1a2234 !important;
      --border-subtle: #1f2937 !important;
      --border-strong: #374151 !important;
      --text-main: #f8fafc !important;
      --text-muted: #94a3b8 !important;
      --primary-accent: #3b82f6 !important;
      --primary-accent-hover: #60a5fa !important;
      --primary-accent-soft: rgba(59, 130, 246, 0.12) !important;
      --shadow-card: 0 20px 40px -15px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.08);
    }

    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background-color: var(--bg-canvas);
      color: var(--text-main);
      font-family: var(--font-sans);
      font-size: 14px;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      transition: background-color 0.25s ease, color 0.25s ease;
    }

    .auth-modal-card {
      width: 100%;
      max-width: 440px;
      background: var(--bg-surface);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-card);
      padding: 36px 32px;
      position: relative;
    }

    @media (max-width: 480px) {
      .auth-modal-card {
        padding: 28px 20px;
        border-radius: var(--radius-md);
      }
    }

    .close-icon-btn {
      position: absolute;
      top: 20px;
      right: 20px;
      background: var(--bg-surface-alt);
      border: 1px solid var(--border-subtle);
      color: var(--text-muted);
      width: 32px;
      height: 32px;
      border-radius: var(--radius-full);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      text-decoration: none;
      transition: var(--transition);
    }
    .close-icon-btn:hover {
      background: var(--border-subtle);
      color: var(--text-main);
    }

    .brand-cluster-top {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 24px;
    }

    .brand-badge-symbol {
      width: 34px;
      height: 34px;
      background: #0f172a;
      color: #ffffff;
      border-radius: var(--radius-xs);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .brand-text-name {
      font-weight: 800;
      font-size: 15px;
      letter-spacing: -0.02em;
    }

    .view-title-main {
      font-size: 26px;
      font-weight: 800;
      letter-spacing: -0.03em;
      color: var(--text-main);
      line-height: 1.2;
    }

    .view-subtitle-desc {
      color: var(--text-muted);
      font-size: 13.5px;
      margin-top: 6px;
      margin-bottom: 24px;
    }

    .form-inputs-stack {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .field-wrap-box {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .field-label-text {
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: var(--text-muted);
    }

    .input-inner-container {
      position: relative;
      display: flex;
      align-items: center;
    }

    .input-leading-icon {
      position: absolute;
      left: 14px;
      color: var(--text-muted);
      display: flex;
      pointer-events: none;
    }

    .custom-input-field {
      background: var(--bg-surface-alt);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-xs);
      padding: 11px 14px 11px 40px;
      font-family: inherit;
      font-size: 14px;
      color: var(--text-main);
      outline: none;
      width: 100%;
      transition: var(--transition);
    }

    .custom-input-field:focus {
      border-color: var(--primary-accent);
      background: var(--bg-surface);
      box-shadow: 0 0 0 3px var(--primary-accent-soft);
    }

    .password-eye-trigger {
      position: absolute;
      right: 12px;
      background: none;
      border: none;
      color: var(--text-muted);
      cursor: pointer;
      display: flex;
      padding: 4px;
    }
    .password-eye-trigger:hover { color: var(--text-main); }

    .forgot-link-wrap {
      display: flex;
      justify-content: flex-end;
      font-size: 12.5px;
      margin-top: 4px;
    }

    .forgot-link-wrap a {
      color: var(--primary-accent);
      text-decoration: none;
      font-weight: 600;
    }
    .forgot-link-wrap a:hover { text-decoration: underline; }

    .primary-submit-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 13px 20px;
      border-radius: var(--radius-xs);
      font-family: inherit;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      border: none;
      background: var(--primary-accent);
      color: #ffffff;
      transition: var(--transition);
      width: 100%;
      margin-top: 6px;
    }
    .primary-submit-btn:hover {
      background: var(--primary-accent-hover);
    }

    .switch-mode-footer {
      text-align: center;
      font-size: 13px;
      color: var(--text-muted);
      margin-top: 24px;
    }

    .switch-mode-footer a {
      color: var(--primary-accent);
      font-weight: 700;
      text-decoration: none;
      cursor: pointer;
    }
    .switch-mode-footer a:hover { text-decoration: underline; }

    .role-cards-deck {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-bottom: 20px;
    }

    .role-choice-card {
      background: var(--bg-surface-alt);
      border: 1.5px solid var(--border-subtle);
      border-radius: var(--radius-sm);
      padding: 14px 16px;
      display: flex;
      align-items: center;
      gap: 14px;
      cursor: pointer;
      transition: var(--transition);
    }

    .role-choice-card:hover {
      border-color: var(--primary-accent);
      background: var(--primary-accent-soft);
      transform: translateY(-2px);
    }

    .role-symbol-avatar {
      width: 40px;
      height: 40px;
      border-radius: var(--radius-xs);
      background: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      color: var(--primary-accent);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .back-btn-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 12.5px;
      font-weight: 700;
      color: var(--text-muted);
      background: none;
      border: none;
      cursor: pointer;
      margin-bottom: 16px;
      padding: 0;
    }
    .back-btn-pill:hover { color: var(--text-main); }

    .d-none { display: none !important; }
  </style>
</head>
<body>

  <div class="auth-modal-card">
    

<!-- Top Right Return Button (Absolute Path) -->
<a href="/DBMSL%20PROJECT/guest_dashboard/guest_portal.php" class="close-icon-btn" title="Return to Portal">
  <i data-lucide="x" style="width: 16px;"></i>
</a>
    <!-- Mini Brand Indicator -->
    <div class="brand-cluster-top">
      <div class="brand-badge-symbol">
        <i data-lucide="building-2" style="width: 18px;"></i>
      </div>
      <span class="brand-text-name">Greenview Heights</span>
    </div>

    <!-- ====================================================================
         PANE 1: LOGIN
         ==================================================================== -->
    <div id="loginSection">
      <div class="view-title-main">Welcome</div>
      <div class="view-subtitle-desc">Login with Email or Phone Number</div>

      <form class="form-inputs-stack" action="process_login.php" method="POST">
        <div class="field-wrap-box">
          <label class="field-label-text">Email or Phone Number</label>
          <div class="input-inner-container">
            <span class="input-leading-icon"><i data-lucide="mail" style="width: 16px;"></i></span>
            <input type="text" name="login_identifier" class="custom-input-field" placeholder="name@example.com or 01XXXXXXXXX" required>
          </div>
        </div>

        <div class="field-wrap-box">
          <label class="field-label-text">Password</label>
          <div class="input-inner-container">
            <span class="input-leading-icon"><i data-lucide="lock" style="width: 16px;"></i></span>
            <input type="password" name="password" id="loginPass" class="custom-input-field" placeholder="••••••••" required>
            <button type="button" class="password-eye-trigger" data-target="loginPass">
              <i data-lucide="eye" style="width: 16px;"></i>
            </button>
          </div>
          <div class="forgot-link-wrap">
            <a href="forgot_password.php">Forgot password?</a>
          </div>
        </div>

        <button type="submit" class="primary-submit-btn">
          <span>LOGIN</span>
        </button>
      </form>

      <div class="switch-mode-footer">
        Don't have an account? <a onclick="showRegisterSelector()">Register Now</a>
      </div>
    </div>

    <!-- ====================================================================
         PANE 2: CHOOSE ROLE SELECTOR
         ==================================================================== -->
    <div id="registerSelectorSection" class="d-none">
      <div class="view-title-main">Choose Account Type</div>
      <div class="view-subtitle-desc">Select your ecosystem role to set up your permissions.</div>

      <div class="role-cards-deck">
        <div class="role-choice-card" onclick="selectAccountRole('guest')">
          <div class="role-symbol-avatar"><i data-lucide="user-check" style="width: 20px;"></i></div>
          <div>
            <div style="font-weight: 800; font-size: 14.5px;">Guest / Visitor</div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Browse inventory and request gate viewing passes.</div>
          </div>
        </div>

        <div class="role-choice-card" onclick="selectAccountRole('owner')">
          <div class="role-symbol-avatar"><i data-lucide="home" style="width: 20px;"></i></div>
          <div>
            <div style="font-weight: 800; font-size: 14.5px;">Flat Owner</div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Manage property deeds, tenant leases, and split ledgers.</div>
          </div>
        </div>

        <div class="role-choice-card" onclick="selectAccountRole('committee')">
          <div class="role-symbol-avatar"><i data-lucide="shield-alert" style="width: 20px;"></i></div>
          <div>
            <div style="font-weight: 800; font-size: 14.5px;">Managing Committee</div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Supervise society funds, approvals, and security passes.</div>
          </div>
        </div>
      </div>

      <div class="switch-mode-footer">
        Already have an account? <a onclick="showLoginSection()">Sign In</a>
      </div>
    </div>

    <!-- ====================================================================
         PANE 3A: GUEST REGISTRATION (Email & Phone Separate)
         ==================================================================== -->
    <div id="registerGuestSection" class="d-none">
      <button class="back-btn-pill" onclick="showRegisterSelector()">
        <i data-lucide="arrow-left" style="width: 14px;"></i> Back to roles
      </button>
      <div class="view-title-main">Guest Registration</div>
      <div class="view-subtitle-desc">Create your visitor pass account for Greenview Heights.</div>

      <form class="form-inputs-stack" action="process_register.php" method="POST">
        <input type="hidden" name="role_id" value="1">
        
        <div class="field-wrap-box">
          <label class="field-label-text">Full Name</label>
          <div class="input-inner-container">
            <span class="input-leading-icon"><i data-lucide="user" style="width: 16px;"></i></span>
            <input type="text" name="full_name" class="custom-input-field" placeholder="Manojkumar Chowdhury" required>
          </div>
        </div>

        <div class="field-wrap-box">
          <label class="field-label-text">Email Address</label>
          <div class="input-inner-container">
            <span class="input-leading-icon"><i data-lucide="mail" style="width: 16px;"></i></span>
            <input type="email" name="email" class="custom-input-field" placeholder="manoj@example.com" required>
          </div>
        </div>

        <div class="field-wrap-box">
          <label class="field-label-text">Phone Number</label>
          <div class="input-inner-container">
            <span class="input-leading-icon"><i data-lucide="phone" style="width: 16px;"></i></span>
            <input type="text" name="phone_number" class="custom-input-field" placeholder="017XXXXXXXX" required>
          </div>
        </div>

        <div class="field-wrap-box">
          <label class="field-label-text">Password</label>
          <div class="input-inner-container">
            <span class="input-leading-icon"><i data-lucide="lock" style="width: 16px;"></i></span>
            <input type="password" name="password" id="regPassGuest" class="custom-input-field" placeholder="••••••••" required>
            <button type="button" class="password-eye-trigger" data-target="regPassGuest">
              <i data-lucide="eye" style="width: 16px;"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="primary-submit-btn">
          <span>COMPLETE GUEST REGISTRATION</span>
        </button>
      </form>
    </div>

    <!-- ====================================================================
         PANE 3B: FLAT OWNER REGISTRATION (Email & Phone Separate)
         ==================================================================== -->
    <div id="registerOwnerSection" class="d-none">
      <button class="back-btn-pill" onclick="showRegisterSelector()">
        <i data-lucide="arrow-left" style="width: 14px;"></i> Back to roles
      </button>
      <div class="view-title-main">Flat Owner Registration</div>
      <div class="view-subtitle-desc">Submit your verification details for property deed records.</div>

      <form class="form-inputs-stack" action="process_register.php" method="POST">
        <input type="hidden" name="role_id" value="3">

        <div class="field-wrap-box">
          <label class="field-label-text">Full Name</label>
          <div class="input-inner-container">
            <span class="input-leading-icon"><i data-lucide="user" style="width: 16px;"></i></span>
            <input type="text" name="full_name" class="custom-input-field" placeholder="Engr. Mahmudul Hasan" required>
          </div>
        </div>

        <div class="field-wrap-box">
          <label class="field-label-text">Email Address</label>
          <div class="input-inner-container">
            <span class="input-leading-icon"><i data-lucide="mail" style="width: 16px;"></i></span>
            <input type="email" name="email" class="custom-input-field" placeholder="owner@example.com" required>
          </div>
        </div>

        <div class="field-wrap-box">
          <label class="field-label-text">Phone Number</label>
          <div class="input-inner-container">
            <span class="input-leading-icon"><i data-lucide="phone" style="width: 16px;"></i></span>
            <input type="text" name="phone_number" class="custom-input-field" placeholder="018XXXXXXXX" required>
          </div>
        </div>

        <div class="field-wrap-box">
          <label class="field-label-text">Password</label>
          <div class="input-inner-container">
            <span class="input-leading-icon"><i data-lucide="lock" style="width: 16px;"></i></span>
            <input type="password" name="password" id="regPassOwner" class="custom-input-field" placeholder="••••••••" required>
            <button type="button" class="password-eye-trigger" data-target="regPassOwner">
              <i data-lucide="eye" style="width: 16px;"></i>
            </button>
          </div>
        </div>

        <div class="field-wrap-box">
          <label class="field-label-text">Holding Tax Number / Deed ID</label>
          <div class="input-inner-container">
            <span class="input-leading-icon"><i data-lucide="file-text" style="width: 16px;"></i></span>
            <input type="text" name="holding_tax_number" class="custom-input-field" placeholder="HT-2026-XXXX" required>
          </div>
        </div>

        <button type="submit" class="primary-submit-btn">
          <span>SUBMIT OWNER VERIFICATION</span>
        </button>
      </form>
    </div>

    <!-- ====================================================================
         PANE 3C: MANAGING COMMITTEE REGISTRATION (Email & Phone Separate)
         ==================================================================== -->
    <div id="registerCommitteeSection" class="d-none">
      <button class="back-btn-pill" onclick="showRegisterSelector()">
        <i data-lucide="arrow-left" style="width: 14px;"></i> Back to roles
      </button>
      <div class="view-title-main">Committee Registration</div>
      <div class="view-subtitle-desc">Administrative credential setup for committee officers.</div>

      <form class="form-inputs-stack" action="process_register.php" method="POST">
        <input type="hidden" name="role_id" value="4">

        <div class="field-wrap-box">
          <label class="field-label-text">Full Name</label>
          <div class="input-inner-container">
            <span class="input-leading-icon"><i data-lucide="user" style="width: 16px;"></i></span>
            <input type="text" name="full_name" class="custom-input-field" placeholder="Zubaer Hossain, FCA" required>
          </div>
        </div>

        <div class="field-wrap-box">
          <label class="field-label-text">Official Email</label>
          <div class="input-inner-container">
            <span class="input-leading-icon"><i data-lucide="mail" style="width: 16px;"></i></span>
            <input type="email" name="email" class="custom-input-field" placeholder="committee@greenview.internal" required>
          </div>
        </div>

        <div class="field-wrap-box">
          <label class="field-label-text">Phone Number</label>
          <div class="input-inner-container">
            <span class="input-leading-icon"><i data-lucide="phone" style="width: 16px;"></i></span>
            <input type="text" name="phone_number" class="custom-input-field" placeholder="019XXXXXXXX" required>
          </div>
        </div>

        <div class="field-wrap-box">
          <label class="field-label-text">Password</label>
          <div class="input-inner-container">
            <span class="input-leading-icon"><i data-lucide="lock" style="width: 16px;"></i></span>
            <input type="password" name="password" id="regPassComm" class="custom-input-field" placeholder="••••••••" required>
            <button type="button" class="password-eye-trigger" data-target="regPassComm">
              <i data-lucide="eye" style="width: 16px;"></i>
            </button>
          </div>
        </div>

        <div class="field-wrap-box">
          <label class="field-label-text">Administrative Secret Key</label>
          <div class="input-inner-container">
            <span class="input-leading-icon"><i data-lucide="key" style="width: 16px;"></i></span>
            <input type="password" name="admin_token" class="custom-input-field" placeholder="Enter security token" required>
          </div>
        </div>

        <button type="submit" class="primary-submit-btn">
          <span>SETUP COMMITTEE ACCOUNT</span>
        </button>
      </form>
    </div>

  </div>

  <script>
    // 1. Theme sync from portal
    function applyPortalTheme() {
      const savedTheme = localStorage.getItem('greenview_theme') || 'light';
      if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
      } else {
        document.documentElement.removeAttribute('data-theme');
      }
      if (window.lucide) lucide.createIcons();
    }
    applyPortalTheme();

    // 2. View Panes Routing Controller
    function showLoginSection() {
      document.getElementById('loginSection').classList.remove('d-none');
      document.getElementById('registerSelectorSection').classList.add('d-none');
      document.getElementById('registerGuestSection').classList.add('d-none');
      document.getElementById('registerOwnerSection').classList.add('d-none');
      document.getElementById('registerCommitteeSection').classList.add('d-none');
    }

    function showRegisterSelector() {
      document.getElementById('loginSection').classList.add('d-none');
      document.getElementById('registerSelectorSection').classList.remove('d-none');
      document.getElementById('registerGuestSection').classList.add('d-none');
      document.getElementById('registerOwnerSection').classList.add('d-none');
      document.getElementById('registerCommitteeSection').classList.add('d-none');
      if (window.lucide) lucide.createIcons();
    }

    function selectAccountRole(role) {
      document.getElementById('registerSelectorSection').classList.add('d-none');
      if (role === 'guest') {
        document.getElementById('registerGuestSection').classList.remove('d-none');
      } else if (role === 'owner') {
        document.getElementById('registerOwnerSection').classList.remove('d-none');
      } else if (role === 'committee') {
        document.getElementById('registerCommitteeSection').classList.remove('d-none');
      }
      if (window.lucide) lucide.createIcons();
    }

    if ('<?= $mode ?>' === 'register') {
      showRegisterSelector();
    }

    // 3. Password Eye Visibility Toggle
    document.querySelectorAll('.password-eye-trigger').forEach(btn => {
      btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        if (input.type === 'password') {
          input.type = 'text';
          btn.innerHTML = '<i data-lucide="eye-off" style="width: 16px;"></i>';
        } else {
          input.type = 'password';
          btn.innerHTML = '<i data-lucide="eye" style="width: 16px;"></i>';
        }
        if (window.lucide) lucide.createIcons();
      });
    });

    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) lucide.createIcons();
    });
  </script>
</body>
</html>