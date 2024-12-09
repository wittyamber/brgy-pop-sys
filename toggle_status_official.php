<?php
    include 'config.php';

    if (isset($_GET['id'], $_GET['status'])) {
        $officialId = $_GET['id'];
        $newStatus = $_GET['status'];

        $query = "UPDATE barangay_officials SET status = ? WHERE official_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $newStatus, $officialId);
        if ($stmt->execute()) {
            echo "Official status updated successfully";
        } else {
            echo "Error updating status: " . $stmt->error;
        }
    }
?>
