<?php
    include '../config.php';

    $id = intval($_GET['id']); // Use intval to sanitize input
    $result = $conn->query("SELECT * FROM users WHERE id = $id");

    if ($result && $result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "User not found"]);
    }
?>
