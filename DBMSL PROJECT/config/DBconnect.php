<?php
// config
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "sn project"; 

// Connection create kora
$conn = new mysqli($servername, $username, $password, $dbname);

// Connection check kora
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

// UTF-8 charset set kora
$conn->set_charset("utf8mb4");
?>