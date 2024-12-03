<?php
    require 'config.php';
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $memberId = $_POST['member_id'];
    
        // Update the member to mark it as archived
        $stmt = $conn->prepare("UPDATE household_members SET archived = 1 WHERE member_id = ?");
        $stmt->bind_param("i", $memberId);
    
        if ($stmt->execute()) {
            session_start();
            $_SESSION['success_message'] = "Resident successfully archived.";
        } else {
            session_start();
            $_SESSION['success_message'] = "Failed to archive member.";
        }
    
        $stmt->close();
        header("Location: household_members.php");
        exit();
    }
?>
