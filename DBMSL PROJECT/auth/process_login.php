<?php
session_start();
require_once __DIR__ . '/../config/DBconnect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: auth.php");
    exit();
}

$login_id = isset($_POST['login_identifier']) ? trim($_POST['login_identifier']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if (empty($login_id) || empty($password)) {
    header("Location: auth.php?error=" . urlencode("Please enter your login details."));
    exit();
}

$stmt = $conn->prepare("SELECT user_id, role_id, full_name, email, phone_number, password_hash FROM users WHERE email = ? OR phone_number = ? LIMIT 1");
$stmt->bind_param("ss", $login_id, $login_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $hashed_pass = $row['password_hash'];
    $isValid = false;

    if (password_verify($password, $hashed_pass) || $password === $hashed_pass) {
        $isValid = true;
    }

    if ($isValid) {
        $_SESSION['user_id']      = (int)$row['user_id'];
        $_SESSION['role_id']      = (int)$row['role_id'];
        $_SESSION['full_name']    = $row['full_name'];
        $_SESSION['email']        = $row['email'];
        $_SESSION['phone_number'] = $row['phone_number'];

        $role = (int)$row['role_id'];

        // Role 6: Super Admin / Sysadmin (FIXED HERE)
        if ($role === 6) {
            $_SESSION['user_role'] = 'ADMIN';
            header("Location: ../admin/admin_dashboard.php");
            exit();
        } elseif ($role === 5) {
            $_SESSION['user_role'] = 'STAFF';
            header("Location: ../staff/staff_dashboard.php");
            exit();
        } elseif ($role === 4) {
            $_SESSION['user_role'] = 'COMMITTEE';
            header("Location: ../committee/committee_dashboard.php");
            exit();
        } elseif ($role === 3) {
            $_SESSION['user_role'] = 'FLAT_OWNER';
            header("Location: ../owner/owner_dashboard.php");
            exit();
        } elseif ($role === 2) {
            $_SESSION['user_role'] = 'TENANT';
            header("Location: ../tenant/tenant_dashboard.php");
            exit();
        } else {
            $_SESSION['user_role'] = 'GUEST';
            header("Location: ../guest_dashboard/guest_portal.php");
            exit();
        }
    }
}

$stmt->close();
$conn->close();
header("Location: auth.php?error=" . urlencode("Invalid credentials entered."));
exit();
?>