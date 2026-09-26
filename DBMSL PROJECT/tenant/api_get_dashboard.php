<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/DBconnect.php';

$userId = $_SESSION['user_id'] ?? 3;

$agreementQuery = "SELECT ta.*, f.flat_number, u.full_name AS owner_name 
                   FROM tenancy_agreements ta
                   JOIN flats f ON ta.flat_id = f.flat_id
                   JOIN users u ON ta.owner_user_id = u.user_id
                   WHERE ta.tenant_user_id = ? AND ta.status = 'ACTIVE' LIMIT 1";
$stmt = $conn->prepare($agreementQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$agreement = $stmt->get_result()->fetch_assoc();

$invoicesQuery = "SELECT * FROM invoices WHERE tenant_user_id = ? ORDER BY invoice_id DESC";
$stmt = $conn->prepare($invoicesQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$invoices = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$amenitiesQuery = "SELECT ab.*, a.amenity_name 
                   FROM amenity_bookings ab
                   JOIN amenities a ON ab.amenity_id = a.amenity_id
                   WHERE ab.user_id = ? ORDER BY ab.booking_id DESC";
$stmt = $conn->prepare($amenitiesQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$amenities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$visitorsQuery = "SELECT * FROM gate_visitors WHERE host_user_id = ? ORDER BY visitor_id DESC";
$stmt = $conn->prepare($visitorsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$visitors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$complaintsQuery = "SELECT * FROM maintenance_complaints WHERE user_id = ? ORDER BY complaint_id DESC";
$stmt = $conn->prepare($complaintsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$complaints = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    "success" => true,
    "data" => [
        "agreement" => $agreement,
        "invoices" => $invoices,
        "amenities" => $amenities,
        "visitors" => $visitors,
        "complaints" => $complaints
    ]
]);