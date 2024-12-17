<?php
    require 'config.php'; 
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $member_id = $_POST['member_id'];
        $last_name = $_POST['last_name'];
        $first_name = $_POST['first_name'];
        $middle_name = $_POST['middle_name'];
        $civil_status = $_POST['civil_status'];
        $occupation = $_POST['occupation'];

        $sql = "UPDATE household_members SET 
                    last_name = ?, 
                    first_name = ?, 
                    middle_name = ?, 
                    civil_status = ?, 
                    occupation = ?  
                WHERE member_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $last_name, $first_name, $middle_name, $civil_status, $occupation, $member_id);

        if ($stmt->execute()) {
            session_start();
            $_SESSION['success_message'] = "Resident updated successfully.";
            header("Location: household_members.php"); 
            exit;
        } else {
            echo "Error updating record: " . $stmt->error;
        }
    }

    header("Location: household_members.php");
    exit();
?>