<?php
// api/guest/get_active_pass.php
header('Content-Type: application/json');
require_once '../config/DBconnect.php';
session_start();

$guest_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;

$sql = "SELECT vb.booking_id, vb.gate_pass_otp, vb.visit_date, vb.start_time, vb.end_time, vb.party_size, vb.booking_status,
               f.flat_number, f.building_block, u.full_name AS owner_name, u.phone_number AS owner_phone
        FROM viewing_bookings vb
        JOIN flats f ON vb.flat_id = f.flat_id
        JOIN users u ON f.owner_id = u.user_id
        WHERE vb.guest_id = $guest_id AND vb.booking_status = 'CONFIRMED'
        ORDER BY vb.booking_id DESC LIMIT 1";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $rawOtp = $row['gate_pass_otp'];
    $formattedOtp = strlen($rawOtp) == 6 ? substr($rawOtp, 0, 3) . '-' . substr($rawOtp, 3, 3) : $rawOtp;

    echo json_encode([
        "success" => true,
        "has_pass" => true,
        "pass" => [
            "booking_id"     => (int)$row['booking_id'],
            "otp"            => $formattedOtp,
            "unit"           => $row['flat_number'] . " (" . $row['building_block'] . ")",
            "schedule"       => $row['visit_date'] . " (" . date('h:i A', strtotime($row['start_time'])) . " – " . date('h:i A', strtotime($row['end_time'])) . ")",
            "party_size"     => $row['party_size'] . " Persons Maximum",
            "terminal"       => "Gate Terminal A (Visitor Bay)",
            "owner_contact"  => $row['owner_name'] . " (" . $row['owner_phone'] . ")",
            "guard_command"  => "+880 1819-001122 (Desk A)"
        ]
    ]);
} else {
    echo json_encode(["success" => true, "has_pass" => false]);
}

$conn->close();
?>