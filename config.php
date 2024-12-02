<?php
// Database connection using MySQLi
$host = "localhost";
$dbname = "brgy_pop_sys";
$username = "root";
$password = "";

// Create a MySQLi connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
