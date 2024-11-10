<?php
    $host = "localhost";
    $dbname = "brgy_pop_sys";
    $username = "root"; // Database username
    $password = ""; // Database password

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>
