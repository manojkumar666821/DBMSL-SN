<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$action = $_POST['action'] ?? '';
$userId = $_SESSION['user_id'] ?? 3;

if ($action === 'send_otp') {
    $otp = (string)random_int(100000, 999999);
    $_SESSION['temp_reset_otp'] = $otp;
    $_SESSION['temp_otp_time'] = time();

    echo json_encode([
        "success" => true,
        "message" => "OTP sent successfully! Demo OTP: " . $otp
    ]);
    exit;
}

if ($action === 'verify_otp') {
    $enteredOtp = trim($_POST['otp'] ?? '');
    $savedOtp = $_SESSION['temp_reset_otp'] ?? '';
    $otpTime = $_SESSION['temp_otp_time'] ?? 0;

    if (empty($savedOtp) || (time() - $otpTime > 300)) {
        echo json_encode(["success" => false, "message" => "OTP expired. Please request a new one."]);
        exit;
    }

    if ($enteredOtp !== $savedOtp) {
        echo json_encode(["success" => false, "message" => "Incorrect OTP! Verification failed."]);
        exit;
    }

    $_SESSION['pwd_verified'] = true;
    unset($_SESSION['temp_reset_otp'], $_SESSION['temp_otp_time']);

    echo json_encode(["success" => true, "message" => "OTP verified successfully!"]);
    exit;
}

echo json_encode(["success" => false, "message" => "Invalid request."]);