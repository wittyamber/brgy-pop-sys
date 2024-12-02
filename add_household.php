<?php
    require 'config.php';
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $last_name = $_POST['last_name'];
        $first_name = $_POST['first_name'];
        $middle_name = $_POST['middle_name'] ?? null;
        $birthdate = $_POST['birthdate'];
        $age = date_diff(date_create($birthdate), date_create('today'))->y; // Calculate age
        $civil_status = $_POST['civil_status'];
        $gender = $_POST['gender'];
        $tribe = $_POST['tribe'] ?? null;
        $occupation = $_POST['occupation'] ?? null;
        $address = $_POST['address'];
        $contact_number = $_POST['contact_number'] ?? null;

        $sql = "INSERT INTO households (
                    last_name, first_name, middle_name, birthdate, age, civil_status, 
                    gender, tribe, occupation, address, contact_number
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssissssss",
            $last_name,
            $first_name,
            $middle_name,
            $birthdate,
            $age,
            $civil_status,
            $gender,
            $tribe,
            $occupation,
            $address,
            $contact_number
        );

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Household head added successfully.";
            header("Location: household.php?msg=Household head added successfully");
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
        $conn->close();

        // After successful addition:
        
        header("Location: household.php");
        exit();
    }
?>
