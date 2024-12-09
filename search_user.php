<?php
    include 'config.php';

    $search = isset($_GET['query']) ? $_GET['query'] : '';
    $search = "%$search%";

    $stmt = $conn->prepare("SELECT * FROM users WHERE username LIKE ?");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($users);

    $stmt->close();
    $conn->close();
?>
