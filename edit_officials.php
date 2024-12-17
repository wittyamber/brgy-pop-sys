<?php
include 'config.php';

// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $official_id = $_POST['official_id'] ?? null;
    $name = $_POST['name'] ?? null;
    $position = $_POST['position'] ?? null;
    $purok = $_POST['purok'] ?? null;
    $contact_number = $_POST['contact_number'] ?? null;
    $email = $_POST['email'] ?? null;
    $date_assigned = $_POST['date_assigned'] ?? null;

    // Update query
    $query = "UPDATE barangay_officials 
              SET name = ?, position = ?, purok_id = ?, contact_number = ?, email = ?, date_assigned = ?
              WHERE official_id = ?";

    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("sssissi", $name, $position, $purok, $contact_number, $email, $date_assigned, $official_id);

        if ($stmt->execute()) {
            // Redirect on success
            header("Location: barangay_officials.php?message=Official Updated Successfully");
            exit;
        } else {
            die("Execution Error: " . $stmt->error);
        }

        $stmt->close();
    } else {
        die("Preparation Error: " . $conn->error);
    }
}
?>
