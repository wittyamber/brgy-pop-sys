<?php
    require 'config.php'; // Include your database connection
    session_start(); // Start the session for success/error messages

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $household_id = $_POST['household_id'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $middle_name = $_POST['middle_name'];
        $birthdate = $_POST['birthdate'];
        $age = $_POST['age'];
        $civil_status = $_POST['civil_status'];
        $gender = $_POST['gender'];
        $relationship_to_head = $_POST['relationship_to_head'];
        $tribe = $_POST['tribe'];
        $occupation = $_POST['occupation'];
        


        $sql = "INSERT INTO household_members (household_id, first_name, last_name, middle_name, birthdate, age, civil_status , gender, relationship_to_head, tribe, occupation) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssisssss", $household_id, $first_name, $last_name, $middle_name , $birthdate, $age, $civil_status, $gender, $relationship_to_head, $tribe, $occupation);

        if ($stmt->execute()) {
            session_start();
            $_SESSION['success_message'] = "Household member added successfully.";
        } else {
            session_start();
            $_SESSION['error_message'] = "Failed to add household member.";
        }

        // Redirect back to the household members page
        header("Location: household_members.php");
        exit;
    }
?>
