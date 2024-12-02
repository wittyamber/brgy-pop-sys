<?php
    require 'config.php'; 
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $household_id = $_POST['household_id'];
        $last_name = $_POST['last_name'];
        $first_name = $_POST['first_name'];
        $middle_name = $_POST['middle_name'];
        $gender = $_POST['gender'];
        $civil_status = $_POST['civil_status'];
        $tribe = $_POST['tribe'];
        $address = $_POST['address'];
        $contact_number = $_POST['contact_number'];

        // Update the database
        $sql = "UPDATE households SET 
                    last_name = ?, 
                    first_name = ?, 
                    middle_name = ?, 
                    gender = ?, 
                    civil_status = ?, 
                    tribe = ?, 
                    address = ?, 
                    contact_number = ? 
                WHERE household_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $last_name, $first_name, $middle_name, $gender, $civil_status, $tribe, $address, $contact_number, $household_id);

        if ($stmt->execute()) {
            // Set the success message after successful update
            $_SESSION['success_message'] = "Household head updated successfully.";
            header("Location: household.php"); // Redirect to the main page
            exit;
        } else {
            // Handle errors (optional)
            echo "Error updating record: " . $stmt->error;
        }
    }

    // Fallback in case of direct access or errors
    header("Location: household.php");
    exit();
?>