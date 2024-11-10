<?php
    include 'config.php'; // Database connection

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $first_name = $_POST['first_name'];
        $middle_name = $_POST['middle_name'];
        $last_name = $_POST['last_name'];
        $birthdate = $_POST['birthdate'];
        $age = $_POST['age']; // Calculated age
        $gender = $_POST['gender'];
        $address = $_POST['address'];
        $contact_number = $_POST['contact_number'];

        // Calculate the age from the birthdate
        $dob = new DateTime($birthdate);
        $now = new DateTime();
        $age = $now->diff($dob)->y;

        $sql = "INSERT INTO residents (first_name, middle_name, last_name, birthdate, age, gender, address, contact_number) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssisss", $first_name, $middle_name, $last_name, $birthdate, $age, $gender, $address, $contact_number);

        if ($stmt->execute()) {
            header("Location: resident_list.php?success=Resident added successfully");
        } else {
            echo "Error: " . $stmt->error;
        }
    }
?>
