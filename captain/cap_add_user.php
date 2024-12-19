<?php
    include '../config.php';
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
        $role = $_POST['role'];

        $stmt = $conn->prepare("INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $username, $password, $role);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "User added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
?>
