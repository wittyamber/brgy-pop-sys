<?php
    include 'config.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id'];

        // Assuming you have an 'archived' column (type TINYINT) in the residents table
        $sql = "UPDATE households SET archived=1 WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: resident_list.php?success=Resident archived successfully");
        } else {
            echo "Error: " . $stmt->error;
        }
    }
?>
