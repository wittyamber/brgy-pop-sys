<?php
require 'config.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch form data
    $member_id = $_POST['member_id'];
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $civil_status = $_POST['civil_status'];
    $occupation = $_POST['occupation'];

    // Log data for debugging
    error_log("Debug Data: ID=$member_id, Last Name=$last_name, First Name=$first_name, Middle Name=$middle_name, Civil Status=$civil_status, Occupation=$occupation");

    // Prepare the SQL query
    $sql = "UPDATE household_members SET 
                last_name = ?, 
                first_name = ?, 
                middle_name = ?, 
                civil_status = ?, 
                occupation = ?  
            WHERE member_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("sssssi", $last_name, $first_name, $middle_name, $civil_status, $occupation, $member_id);

    // Execute and handle success/failure
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0 || $stmt->errno === 0) {
            $_SESSION['success_message'] = "Resident updated successfully.";
        } else {
            $_SESSION['error_message'] = "No changes were made or invalid ID.";
        }
        header("Location: household_members.php"); 
        exit;
    } else {
        // Log SQL errors
        error_log("SQL Error: " . $stmt->error);
        echo "Error updating record: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
    exit;
}
?>
