<?php
    include 'config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $officialId = $_POST['officialId'];
        $name = $_POST['officialName'];
        $position = $_POST['officialPosition'];
        $address = $_POST['officialAddress'];
        $contactNumber = $_POST['officialContactNumber'];
        $email = $_POST['officialEmail'];
        $dateAssigned = $_POST['officialDateAssigned'];

        $status = 'Active'; // Default status for new officials

        $query = "INSERT INTO barangay_officials (official_id, name, position, address, contact_number, email, status, date_assigned) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssssssss', $officialId, $name, $position, $address, $contactNumber, $email, $status, $dateAssigned);

        if ($stmt->execute()) {
            echo "Barangay official added successfully.";
        } else {
            echo "Error adding official: " . $conn->error;
        }

        $stmt->close();
        $conn->close();
    }
?>