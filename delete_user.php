<?php
include 'config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Check if an ID was passed
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No user ID provided");
}

$userId = $_GET['id'];

try {
    // Prepare and execute delete statement
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $result = $stmt->execute([$userId]);

    if ($result) {
        // Redirect back to user management with success message
        header("Location: user_management.php?success=User deleted successfully");
        exit();
    } else {
        // If deletion fails
        header("Location: user_management.php?error=Failed to delete user");
        exit();
    }
} catch(PDOException $e) {
    // Handle potential database errors
    header("Location: user_management.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>