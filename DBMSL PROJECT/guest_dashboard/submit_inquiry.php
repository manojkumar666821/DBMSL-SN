<?php
// api/guest/submit_inquiry.php
header('Content-Type: application/json');
require_once '../config/DBconnect.php';
session_start();

$guest_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;
$flat_id  = isset($_POST['flat_id']) ? (int)$_POST['flat_id'] : 1; // Default to community flat 1 if general
$message  = isset($_POST['message']) ? $conn->real_escape_string(trim($_POST['message'])) : '';

if (empty($message)) {
    echo json_encode(["success" => false, "message" => "Message cannot be empty."]);
    exit;
}

$sql = "INSERT INTO inquiries (flat_id, guest_id, message_payload, status) 
        VALUES ($flat_id, $guest_id, '$message', 'OPEN')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Inquiry successfully logged with Society Office."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to save inquiry: " . $conn->error]);
}

$conn->close();
?>