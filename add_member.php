<?php
    require 'config.php'; 
    session_start(); 

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $household_id = htmlspecialchars($_POST['household_id'], ENT_QUOTES, 'UTF-8');
        $first_name = htmlspecialchars($_POST['first_name'], ENT_QUOTES, 'UTF-8');
        $last_name = htmlspecialchars($_POST['last_name'], ENT_QUOTES, 'UTF-8');
        $middle_name = htmlspecialchars($_POST['middle_name'], ENT_QUOTES, 'UTF-8');
        $birthdate = $_POST['birthdate'];
        $age = $_POST['age'];
        $civil_status = $_POST['civil_status'];
        $gender = $_POST['gender'];
        $relationship_to_head = $_POST['relationship_to_head'];
        $tribe = $_POST['tribe'];
        $occupation = $_POST['occupation'];
        $purok_id = $_POST['purok_id']; 

        if (!empty($household_id) && !empty($first_name) && !empty($last_name) && !empty($birthdate) && !empty($gender) && !empty($purok_id)) {
            $sql = "INSERT INTO household_members (household_id, first_name, last_name, middle_name, birthdate, age, civil_status, gender, relationship_to_head, tribe, occupation, purok_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("issssissssss", $household_id, $first_name, $last_name, $middle_name, $birthdate, $age, $civil_status, $gender, $relationship_to_head, $tribe, $occupation, $purok_id);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Household member added successfully.";
                } else {
                    $_SESSION['error_message'] = "Failed to add household member: " . $stmt->error;
                }
            } else {
                $_SESSION['error_message'] = "Failed to prepare the statement: " . $conn->error;
            }
        } else {
            $_SESSION['error_message'] = "Please fill in all required fields.";
        }

        // Redirect back to the household members page
        header("Location: household_members.php");
        exit;
    }
?>
