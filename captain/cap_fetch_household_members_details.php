<?php
    include('../config.php');

    $memberId = $_GET['id'];

    // Debugging: Check if ID is received
    if (!isset($memberId)) {
        echo json_encode(['error' => 'No member ID received']);
        exit;
    }

    // SQL query
    $query = "
        SELECT 
            hm.first_name, 
            hm.last_name, 
            hm.age, 
            hm.gender, 
            hm.tribe, 
            hm.occupation,
            h.household_number, 
            CONCAT(hh.first_name, ' ', hh.last_name) AS household_head_name,
            p.purok_name
        FROM household_members hm
        LEFT JOIN household h ON hm.household_id = h.household_id
        LEFT JOIN household_members hh ON h.household_head_id = hh.member_id
        LEFT JOIN purok p ON h.purok_id = p.purok_id
        WHERE hm.member_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $memberId);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    // Debugging: Check SQL query and result
    if (!$data) {
        echo json_encode(['error' => 'No data found for this ID']);
    } else {
        echo json_encode($data);
    }
?>
