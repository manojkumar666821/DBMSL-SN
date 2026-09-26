<?php
// ============================================================================
// ARCHITECTURE: Robust Registration Processing Engine
// FILE: auth/process_register.php
// ============================================================================
session_start();
require_once '../config/DBconnect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: auth.php?mode=register");
    exit();
}

$role_id      = isset($_POST['role_id']) ? (int)$_POST['role_id'] : 1;
$full_name    = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$email        = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
$password     = isset($_POST['password']) ? trim($_POST['password']) : '';

// 1. Mandatory Fields Validation
if (empty($full_name) || empty($email) || empty($phone_number) || empty($password)) {
    header("Location: auth.php?mode=register&error=" . urlencode("All mandatory fields must be completed."));
    exit();
}

// 2. Email Validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: auth.php?mode=register&error=" . urlencode("Invalid email format."));
    exit();
}

// 3. Bangladesh Mobile Validation
$clean_phone = preg_replace('/[^0-9]/', '', $phone_number);
if (strlen($clean_phone) > 11 && (substr($clean_phone, 0, 2) === '88')) {
    $clean_phone = substr($clean_phone, 2);
}
if (!preg_match('/^01[3-9]\d{8}$/', $clean_phone)) {
    header("Location: auth.php?mode=register&error=" . urlencode("Invalid Bangladesh phone number. Use 01XXXXXXXXX"));
    exit();
}
$phone_number = $clean_phone;

// 4. Role-Gated Committee Secret Key Check
if ($role_id === 4) {
    $admin_token = isset($_POST['admin_token']) ? trim($_POST['admin_token']) : '';
    $master_key  = "GREENVIEW_COMMITTEE_2026";
    if ($admin_token !== $master_key) {
        header("Location: auth.php?mode=register&error=" . urlencode("Unauthorized committee key provided."));
        exit();
    }
}

// 5. Check Duplicate Email & Phone
$stmt = $conn->prepare("SELECT user_id, email, phone_number FROM users WHERE email = ? OR phone_number = ? LIMIT 1");
$stmt->bind_param("ss", $email, $phone_number);
$stmt->execute();
$check_res = $stmt->get_result();

if ($existing = $check_res->fetch_assoc()) {
    $stmt->close();
    if (strtolower($existing['email']) === strtolower($email)) {
        header("Location: auth.php?mode=register&error=" . urlencode("This email address is already registered. Please sign in."));
    } else {
        header("Location: auth.php?mode=register&error=" . urlencode("This phone number is already registered."));
    }
    exit();
}
$stmt->close();

// 6. Secure Password Hashing
$password_hash = password_hash($password, PASSWORD_BCRYPT);
$is_verified   = 1; // Instant access enable

// 7. Insert into Core Users Table (ড্রপ করা টেবিল ছাড়াই সরাসরি users টেবিলে)
$insert = $conn->prepare("INSERT INTO users (role_id, full_name, email, phone_number, password_hash, is_verified) VALUES (?, ?, ?, ?, ?, ?)");
$insert->bind_param("issssi", $role_id, $full_name, $email, $phone_number, $password_hash, $is_verified);

if ($insert->execute()) {
    $new_user_id = $insert->insert_id;
    $insert->close();

    // 8. Hydrate Session
    $_SESSION['user_id']      = $new_user_id;
    $_SESSION['full_name']    = $full_name;
    $_SESSION['email']        = $email;
    $_SESSION['phone_number'] = $phone_number;
    $_SESSION['role_id']      = $role_id;

    // 9. Role-Specific Direct Redirection (গেস্টে ফোর্স করা লাইন বাদ দেওয়া হয়েছে)
    if ($role_id === 3) {
        $_SESSION['user_role'] = 'FLAT_OWNER';
        header("Location: ../owner/owner_dashboard.php");
    } elseif ($role_id === 2) {
        $_SESSION['user_role'] = 'TENANT';
        header("Location: ../tenant/tenant_dashboard.php");
    } elseif ($role_id === 4) {
        $_SESSION['user_role'] = 'COMMITTEE';
        header("Location: ../committee/committee_dashboard.php");
    } else {
        $_SESSION['user_role'] = 'GUEST';
        header("Location: ../guest_dashboard/guest_portal.php");
    }
    exit();

} else {
    $error_desc = $conn->error;
    $insert->close();
    header("Location: auth.php?mode=register&error=" . urlencode("Registration failed: " . $error_desc));
    exit();
}
?>