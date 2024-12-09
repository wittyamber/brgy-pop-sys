<?php
    include 'config.php';

    $id = intval($_GET['id']); // Use intval to sanitize input
    $result = $conn->query("SELECT * FROM users WHERE id = $id");

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "
            <p><strong>ID:</strong> {$user['id']}</p>
            <p><strong>Name:</strong> {$user['name']}</p>
            <p><strong>Username:</strong> {$user['username']}</p>
            <p><strong>Role:</strong> {$user['role']}</p>
            <p><strong>Status:</strong> {$user['status']}</p>
        ";
    } else {
        echo "User not found!";
    }
?>
