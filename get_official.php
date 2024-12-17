<?php
include 'config.php';

if (isset($_GET['id'])) {
    $official_id = $_GET['id'];
    $query = "SELECT bo.official_id, bo.name, bo.position, bo.purok_id, bo.contact_number, bo.email, bo.status, bo.date_assigned, 
            p.purok_name
            FROM barangay_officials AS bo
            LEFT JOIN puroks AS p ON bo.purok_id = p.purok_id
            WHERE bo.official_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $official_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $official = $result->fetch_assoc();

    if ($official) {
        echo json_encode($official);
    } else {
        echo json_encode(['error' => 'Official not found']);
    }
}
?>
