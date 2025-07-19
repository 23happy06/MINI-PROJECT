<?php
// Database configuration
$servername = "localhost"; // XAMPP default
$username = "root";        // XAMPP default user
$password = "";            // XAMPP default has no password
$dbname = "shiningstudents";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Uncomment this line to confirm DB connection during testing
// echo "Connected successfully to database!";
?>
