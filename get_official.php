<?php
    include 'config.php';

    if (isset($_GET['id'])) {
        $officialId = $_GET['id'];

        $query = "SELECT * FROM barangay_officials WHERE official_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $officialId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode($result->fetch_assoc());
        } else {
            echo json_encode(["error" => "Official not found"]);
        }
    }
?>
