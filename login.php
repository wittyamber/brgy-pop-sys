<?php
    session_start();
    include 'config.php'; // Database connection

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = md5($_POST['password']); // Password should be hashed

        $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
        switch ($user['role']) {
            case 'Admin':
                header("Location: dashboard.php");
                break;
            case 'Captain':
                header("Location: captain/captain_ui.php");
                break;
            case 'Secretary':
                header("Location: secretary/secretary_ui.php");
                break;
            case 'Staff':
                header("Location: staff_ui.php");
                break;
            case 'BHW':
                header("Location: bhw_ui.php");
                break;
        }
            
            // Redirect based on role
            //header("Location: dashboard.php");
            exit;
        } else {
            header("Location: index.php?error=Invalid username or password");
            exit;
        }
    }
?>
