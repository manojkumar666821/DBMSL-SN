<?php
// api/guest/book_viewing.php
header('Content-Type: application/json');
require_once '../config/DBconnect.php';
session_start();

$guest_id   = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1; // Fallback to guest id 1 if not in session
$flat_id    = isset($_POST['flat_id']) ? (int)$_POST['flat_id'] : 0;
$visit_date = isset($_POST['visit_date']) ? $conn->real_escape_string(trim($_POST['visit_date'])) : '';
$slot_time  = isset($_POST['slot_time']) ? $conn->real_escape_string(trim($_POST['slot_time'])) : '10:30:00';
$party_size = isset($_POST['party_size']) ? (int)$_POST['party_size'] : 2;

if ($flat_id <= 0 || empty($visit_date)) {
    echo json_encode(["success" => false, "message" => "Missing required fields."]);
    exit;
}

// Generate 6-digit Gate OTP
$gate_otp = strval(rand(100000, 999999));
$start_time = $slot_time;
$end_time   = date('H:i:s', strtotime('+1 hour', strtotime($start_time)));

$sql = "INSERT INTO viewing_bookings (flat_id, guest_id, visit_date, start_time, end_time, party_size, gate_pass_otp, booking_status) 
        VALUES ($flat_id, $guest_id, '$visit_date', '$start_time', '$end_time', $party_size, '$gate_otp', 'CONFIRMED')";

if ($conn->query($sql) === TRUE) {
    $booking_id = $conn->insert_id;
    
    // Fetch target flat info for pass slip
    $flatQuery = $conn->query("SELECT f.flat_number, f.building_block, u.full_name AS owner_name, u.phone_number AS owner_phone 
                               FROM flats f JOIN users u ON f.owner_id = u.user_id WHERE f.flat_id = $flat_id");
    $flatInfo = $flatQuery->fetch_assoc();

    echo json_encode([
        "success" => true,
        "message" => "Viewing pass issued successfully.",
        "pass" => [
            "booking_id"     => $booking_id,
            "gate_otp"       => substr($gate_otp, 0, 3) . '-' . substr($gate_otp, 3, 3),
            "unit"           => $flatInfo['flat_number'] . " (" . $flatInfo['building_block'] . ")",
            "schedule"       => "$visit_date ($start_time)",
            "party_size"     => $party_size,
            "terminal"       => "Gate Terminal A (Visitor Bay)",
            "owner_contact"  => $flatInfo['owner_name'] . " (" . $flatInfo['owner_phone'] . ")",
            "guard_command"  => "+880 1819-001122 (Duty Desk)"
        ]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to book appointment: " . $conn->error]);
}

$conn->close();
?>