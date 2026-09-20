<?php
session_start();
require_once '../config/DBconnect.php';

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

// Query user by email or phone
$stmt = $conn->prepare("SELECT user_id, role_id, full_name, email, phone_number, password_hash, is_verified FROM users WHERE email = ? OR phone_number = ? LIMIT 1");
$stmt->bind_param("ss", $login_id, $login_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $hashed_pass = $row['password_hash'];
    $isValid = false;

    // Verify hashed password or legacy plain check
    if (password_verify($password, $hashed_pass) || $password === $hashed_pass) {
        $isValid = true;
    }

    if ($isValid) {
        // Hydrate Session Environment
        $_SESSION['user_id']      = (int)$row['user_id'];
        $_SESSION['role_id']      = (int)$row['role_id'];
        $_SESSION['full_name']    = $row['full_name'];
        $_SESSION['email']        = $row['email'];
        $_SESSION['phone_number'] = $row['phone_number'];

        $stmt->close();
        $conn->close();

        // Redirect based on Ecosystem Role
        switch ((int)$row['role_id']) {
            case 3:
                header("Location: ../views/owner/owner_dashboard.php");
                break;
            case 4:
                header("Location: ../views/admin/committee_dashboard.php");
                break;
            case 2:
                header("Location: ../views/tenant/tenant_dashboard.php");
                break;
            default:
                header("Location: ../guest_dashboard/guest_portal.php");
                break;
        }
        exit();
    }
}

$stmt->close();
$conn->close();
header("Location: auth.php?error=" . urlencode("Invalid credentials entered."));
exit();
?>