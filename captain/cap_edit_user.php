<?php
    include '../config.php';

    $id = $_POST['id'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    $sql = "UPDATE users SET name = '$name', username = '$username', role = '$role' WHERE id = $id";
    $conn->query($sql);

    echo "User updated successfully!";
?>
