<?php
// process_user.php - Process form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection (MySQLi)
    include 'config.php';  // Ensure the path to config.php is correct
    
    // Gather data from the form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = $_POST['status'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        // Prepare the query using MySQLi
        $stmt = $conn->prepare("INSERT INTO users (name, email, role, status, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $role, $status, $password);  // Bind the parameters
        
        // Execute the query
        $stmt->execute();
        
        // Redirect to users page after successful insert
        header("Location: user_management.php");
        exit; // Ensure the script stops executing after the redirect
    } catch(Exception $e) {
        // If an error occurs, display it
        echo "Error: " . $e->getMessage();
    }
}
?>
