<?php
    include 'config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize inputs
        $name = $_POST['officialName'];
        $position = $_POST['officialPosition'];
        $purokId = $_POST['officialPurok'];
        $contactNumber = $_POST['officialContactNumber'];
        $email = $_POST['officialEmail'];
        $dateAssigned = $_POST['officialDateAssigned'];
        $status = 'Active'; // Default status for new officials

        // Prepare and execute the SQL query
        $query = "INSERT INTO barangay_officials (name, position, purok_id, contact_number, email, status, date_assigned) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            die("Query preparation failed: " . $conn->error);
        }

        // Bind parameters (s = string, i = integer)
        $stmt->bind_param('ssissss', $name, $position, $purokId, $contactNumber, $email, $status, $dateAssigned);

        // Execute and handle errors
        if ($stmt->execute()) {
            header("Location: barangay_officials.php?success=1");
            exit();
        } else {
            error_log("Error adding official: " . $stmt->error);
            echo "Error adding official: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
?>
