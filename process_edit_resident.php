<?php
    include 'config.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id'];
        $first_name = $_POST['first_name'];
        $middle_name = $_POST['middle_name'];
        $last_name = $_POST['last_name'];
        $birthdate = $_POST['birthdate'];
        $address = $_POST['address'];
        $contact_number = $_POST['contact_number'];

        // Calculate the age from the birthdate
        $dob = new DateTime($birthdate);
        $now = new DateTime();
        $age = $now->diff($dob)->y;

        $sql = "UPDATE households SET first_name=?, middle_name=?, last_name=?, birthdate=?, age=?, address=?, contact_number=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssissi", $first_name, $middle_name, $last_name, $birthdate, $age, $address, $contact_number, $id);

        if ($stmt->execute()) {
            header("Location: resident_list.php?success=Resident updated successfully");
        } else {
            echo "Error: " . $stmt->error;
        }
    }
?>
