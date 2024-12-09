<?php
    require '../config.php'; 
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $household_id = $_POST['household_id'];
        $last_name = $_POST['last_name'];
        $first_name = $_POST['first_name'];
        $middle_name = $_POST['middle_name'];
        $birthdate = $_POST['birthdate'];
        $gender = $_POST['gender'];
        $civil_status = $_POST['civil_status'];
        $tribe = $_POST['tribe'];
        $occupation = $_POST['occupation'];
        $address = $_POST['address'];
        $contact_number = $_POST['contact_number'];

        // Update the database
        $sql = "UPDATE households SET 
                    last_name = ?, 
                    first_name = ?, 
                    middle_name = ?, 
                    birthdate = ?,
                    gender = ?, 
                    civil_status = ?, 
                    tribe = ?,
                    occupation =  ?, 
                    address = ?, 
                    contact_number = ? 
                WHERE household_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssi", $last_name, $first_name, $middle_name, $birthdate, $gender, $civil_status, $occupation, $tribe ,$address, $contact_number, $household_id);

        if ($stmt->execute()) {
            // Set the success message after successful update
            session_start();
            $_SESSION['success_message'] = "Household head updated successfully.";
            header("Location: cap_household.php"); // Redirect to the main page
            exit;
        } else {
            // Handle errors (optional)
            echo "Error updating record: " . $stmt->error;
        }
    }

    // Fallback in case of direct access or errors
    header("Location: cap_household.php");
    exit();
?>