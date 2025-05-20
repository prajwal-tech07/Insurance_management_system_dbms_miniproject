<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "Prajwal@123."; // Set your MySQL password if you have one
$database = "insurance_management";

// Create database connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
