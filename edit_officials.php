<?php
    require 'config.php';  // Include your database connection

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $official_id = $_POST['official_id']; 
        $name = $_POST['name'];
        $position = $_POST['position'];
        $address = $_POST['address'];
        $contact_number = $_POST['contact_number'];
        $email = $_POST['email'];
        $date_assigned = $_POST['date_assigned'];

        $sql = "UPDATE barangay_officials 
                SET name = ?, position = ?, address = ?, contact_number = ?, email = ?, date_assigned = ? 
                WHERE official_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $name, $position, $address, $contact_number, $email, $date_assigned, $official_id);

        if ($stmt->execute()) {
            header("Location: barangay_officials.php?success=1");
            exit();
        } else {
            error_log("Error updating official: " . $stmt->error);
            echo "Error updating official: " . $stmt->error;
        }
        $stmt->close();
    }

    $conn->close();
?>
