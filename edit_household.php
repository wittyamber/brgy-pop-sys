<?php
    require 'config.php'; 
    session_start();

    if (isset($_POST['update_household'])) {
        $household_id = $_POST['household_id'];
        $household_number = $_POST['household_number'];
        $purok_id = $_POST['purok_id'];
        $household_head_id = $_POST['household_head_id'];
        $contact_number = $_POST['contact_number'];
        $total_members = $_POST['total_members'];
    
        $sql = "UPDATE household 
                SET household_number = ?, purok_id = ?, household_head_id = ?, contact_number = ?, total_members = ? 
                WHERE household_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siisii", $household_number, $purok_id, $household_head_id, $contact_number, $total_members, $household_id);
    
        if ($stmt->execute()) {
            echo "Household updated successfully!";
        } else {
            echo "Error updating household: " . $conn->error;
        }
    }
?>
