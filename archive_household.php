<?php
    require 'config.php';
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $householdId = $_POST['household_id'];
    
        // Update the household to mark it as archived
        $stmt = $conn->prepare("UPDATE households SET archived = 1 WHERE household_id = ?");
        $stmt->bind_param("i", $householdId);
    
        if ($stmt->execute()) {
            session_start();
            $_SESSION['success_message'] = "Household successfully archived.";
        } else {
            session_start();
            $_SESSION['success_message'] = "Failed to archive household.";
        }
    
        $stmt->close();
        header("Location: household.php");
        exit();
    }
?>
