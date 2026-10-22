<?php
//Database connection code
$host = "db";
$dbname = "support_ticketing";
$username = "ticket_user";
$password = "ticketpass";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>