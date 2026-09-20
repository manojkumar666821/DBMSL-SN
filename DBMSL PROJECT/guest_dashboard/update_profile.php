<?php
// api/guest/update_profile.php
header('Content-Type: application/json');
require_once '../config/DBconnect.php';
session_start();

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;
$action  = isset($_POST['action']) ? trim($_POST['action']) : 'profile';

if ($action === 'profile') {
    $full_name = isset($_POST['full_name']) ? $conn->real_escape_string(trim($_POST['full_name'])) : '';
    $phone     = isset($_POST['phone_number']) ? $conn->real_escape_string(trim($_POST['phone_number'])) : '';

    if (empty($full_name) || empty($phone)) {
        echo json_encode(["success" => false, "message" => "Name and phone cannot be empty."]);
        exit;
    }

    $sql = "UPDATE users SET full_name = '$full_name', phone_number = '$phone' WHERE user_id = $user_id";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['full_name'] = $full_name;
        $_SESSION['phone_number'] = $phone;
        echo json_encode(["success" => true, "message" => "Profile updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Update failed: " . $conn->error]);
    }
} elseif ($action === 'password') {
    $new_pass = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    
    if (strlen($new_pass) < 8) {
        echo json_encode(["success" => false, "message" => "Password must be at least 8 characters long."]);
        exit;
    }

    $password_hash = password_hash($new_pass, PASSWORD_BCRYPT);
    $sql = "UPDATE users SET password_hash = '$password_hash' WHERE user_id = $user_id";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Password updated securely."]);
    } else {
        echo json_encode(["success" => false, "message" => "Password change failed: " . $conn->error]);
    }
}

$conn->close();
?>