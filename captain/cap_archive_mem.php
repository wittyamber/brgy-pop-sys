<?php
    require '../config.php';
    session_start();

    // Debugging: Log POST data
    error_log(print_r($_POST, true));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['member_id']) && is_numeric($_POST['member_id'])) {
            $memberId = intval($_POST['member_id']); // Sanitize input

            $stmt = $conn->prepare("UPDATE household_members SET archived = 1, status = 'Archived' WHERE member_id = ?");
            $stmt->bind_param("i", $memberId);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Resident successfully archived.";
            } else {
                $_SESSION['error_message'] = "Failed to archive resident: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $_SESSION['error_message'] = "Invalid member ID.";
            error_log("Invalid member ID: " . ($_POST['member_id'] ?? 'Not set'));
        }
    } else {
        $_SESSION['error_message'] = "Invalid request method.";
    }

    header("Location: cap_household_members.php");
    exit();
?>
