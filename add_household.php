<?php
    require 'config.php';
    session_start();

    if (isset($_POST['add_household'])) {
        $household_number = $_POST['household_number'];
        $purok_id = $_POST['purok_id'];
        $household_head_id = $_POST['household_head'];
        $contact_number = $_POST['contact_number'];
        $total_members = $_POST['total_members'];

        // Adjust SQL query to match the table structure
        $sql = "INSERT INTO household (household_number, purok_id, household_head_id, contact_number, total_members) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siisi", $household_number, $purok_id, $household_head_id, $contact_number, $total_members);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Household added successfully!";
            header("Location: household.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error adding household: " . $conn->error;
            error_log($conn->error); // Log error if needed
            header("Location: household.php");
            exit();
        }
    }
?>
