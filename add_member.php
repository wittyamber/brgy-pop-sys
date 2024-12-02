<?php
    require 'config.php'; // Include your database connection
    session_start(); // Start the session for success/error messages

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $household_id = $_POST['household_id'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $relationship_to_head = $_POST['relationship_to_head'];
        $birthdate = $_POST['birthdate'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];

        $sql = "INSERT INTO household_members (household_id, first_name, last_name, relationship_to_head, birthdate, age, gender) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssis", $household_id, $first_name, $last_name, $relationship_to_head, $birthdate, $age, $gender);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Household member added successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to add household member.";
        }

        // Redirect back to the household members page
        header("Location: household_members.php");
        exit;
    }
?>
