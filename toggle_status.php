<?php
    include 'config.php';

    $id = $_GET['id'];
    $status = $_GET['status'];

    $sql = "UPDATE users SET status = '$status' WHERE id = $id";
    $conn->query($sql);

    echo "User status updated to $status!";
?>
