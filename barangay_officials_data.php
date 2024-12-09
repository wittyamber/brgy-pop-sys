<?php
    include 'config.php';

    $query = "SELECT * FROM barangay_officials";
    $result = mysqli_query($conn, $query);

    $officials = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $officials[] = $row;
    }

    echo json_encode(['officials' => $officials]);
?>
