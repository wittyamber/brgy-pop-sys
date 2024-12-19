<?php
session_start();
include 'config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Query to get user details
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Debug: Log role and password
        error_log("Role for {$username}: " . $user['role']);
        error_log("Stored password hash: " . $user['password']);

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store user details in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            switch (trim($user['role'])) { // Trim whitespace
                case 'Admin':
                    error_log("Redirecting Admin to dashboard.php");
                    header("Location: dashboard.php");
                    break;
                case 'Captain':
                    error_log("Redirecting Captain to captain_ui.php");
                    header("Location: captain/captain_ui.php");
                    break;
                case 'Secretary':
                    error_log("Redirecting Secretary to secretary_ui.php");
                    header("Location: secretary/secretary_ui.php");
                    break;
                case 'BHW':
                    error_log("Redirecting BHW to bhw_ui.php");
                    header("Location: bhw_ui.php");
                    break;
                default:
                    $_SESSION['error_message'] = "Invalid role detected.";
                    error_log("Invalid role detected for user: {$username}");
                    header("Location: index.php");
                    break;
            }
            exit;
        } else {
            error_log("Password verification failed for {$username}");
            $_SESSION['error_message'] = "Invalid username or password.";
            header("Location: index.php");
            exit;
        }
    } else {
        error_log("No user found with username: {$username}");
        $_SESSION['error_message'] = "Invalid username or password.";
        header("Location: index.php");
        exit;
    }
}
?>
