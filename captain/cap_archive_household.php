<?php
    require '../config.php';
    session_start();

    if (isset($_POST['archive_household'])) {
        $household_id = $_POST['household_id'];
    
        $sql = "UPDATE household SET archived = 1, status = 'Archived' WHERE household_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $household_id);
    
        if ($stmt->execute()) {
            $_SESSION['message'] = "Household archived successfully!";
            header("Location: cap_household.php");
            exit();
        } else {
            $_SESSION['error'] = "Error archiving household: " . $conn->error;
            header("Location: cap_household.php");
            exit();
        }
    }
?>
