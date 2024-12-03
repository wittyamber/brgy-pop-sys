<?php
include 'config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = htmlspecialchars($_POST['name']);
        $role = htmlspecialchars($_POST['role']);
        $status = htmlspecialchars($_POST['status']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (name, role, status, password) VALUES (:name, :role, :status, :password)");
        $stmt->execute([
            ':name' => $name,
            ':role' => $role,
            ':status' => $status,
            ':password' => $password,
        ]);

        // Redirect to the user management page with success message
        header("Location: user_management.php?success=1");
        exit;
    }
} catch (PDOException $e) {
    echo "Error saving user: " . $e->getMessage();
}
?>
