<?php
// ============================================================================
// ARCHITECTURE: Registration Processing Engine
// FILE: auth/process_register.php
// ============================================================================
session_start();
require_once '../config/DBconnect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: auth.php");
    exit();
}

$role_id      = isset($_POST['role_id']) ? (int)$_POST['role_id'] : 1;
$full_name    = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$email        = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
$password     = isset($_POST['password']) ? trim($_POST['password']) : '';

// Validation Guard
if (empty($full_name) || empty($email) || empty($phone_number) || empty($password)) {
    header("Location: auth.php?mode=register&error=" . urlencode("All mandatory fields must be completed."));
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: auth.php?mode=register&error=" . urlencode("Invalid email address format."));
    exit();
}

// Bangladesh Phone Validation Pattern
if (!preg_match('/^(?:\+88|88)?(01[3-9]\d{8})$/', $phone_number)) {
    header("Location: auth.php?mode=register&error=" . urlencode("Invalid Bangladesh phone number. Format: 01XXXXXXXXX"));
    exit();
}

// Committee Secret Key Check
if ($role_id === 4) {
    $admin_token = isset($_POST['admin_token']) ? trim($_POST['admin_token']) : '';
    $master_key  = "GREENVIEW_COMMITTEE_2026";
    if ($admin_token !== $master_key) {
        header("Location: auth.php?mode=register&error=" . urlencode("Unauthorized committee key provided."));
        exit();
    }
}

// Duplicate User Check
$stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? OR phone_number = ? LIMIT 1");
$stmt->bind_param("ss", $email, $phone_number);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    header("Location: auth.php?mode=register&error=" . urlencode("Email or Phone Number is already registered."));
    exit();
}
$stmt->close();

// Password Hashing
$password_hash = password_hash($password, PASSWORD_BCRYPT);
$is_verified   = ($role_id === 1) ? 1 : 0;

// Insert Query
$insert = $conn->prepare("INSERT INTO users (role_id, full_name, email, phone_number, password_hash, is_verified) VALUES (?, ?, ?, ?, ?, ?)");
$insert->bind_param("issssi", $role_id, $full_name, $email, $phone_number, $password_hash, $is_verified);

if ($insert->execute()) {
    $new_user_id = $insert->insert_id;
    $insert->close();

    // Owner Verification Log
    if ($role_id === 3 && !empty($_POST['holding_tax_number'])) {
        $tax_no = trim($_POST['holding_tax_number']);
        $owner_stmt = $conn->prepare("INSERT INTO owner_verifications (user_id, holding_tax_number, status) VALUES (?, ?, 'PENDING')");
        if ($owner_stmt) {
            $owner_stmt->bind_param("is", $new_user_id, $tax_no);
            $owner_stmt->execute();
            $owner_stmt->close();
        }
    }

    // Session Setup
    $_SESSION['user_id']      = $new_user_id;
    $_SESSION['full_name']    = $full_name;
    $_SESSION['email']        = $email;
    $_SESSION['phone_number'] = $phone_number;
    $_SESSION['role_id']      = $role_id;

    // Role-based Forwarding (Clean branching)
    if ($role_id === 3) {
        header("Location: ../views/owner/owner_dashboard.php");
        exit();
    } elseif ($role_id === 4) {
        header("Location: ../views/admin/committee_dashboard.php");
        exit();
    } else {
        header("Location: ../guest_dashboard/guest_portal.php");
        exit();
    }
} else {
    $insert->close();
    header("Location: auth.php?mode=register&error=" . urlencode("System failure during registration. Please retry."));
    exit();
}
?>