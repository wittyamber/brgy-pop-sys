<?php
include 'config.php'; // Include your database connection

if (isset($_GET['household_d'])) {
    $household_head_id = intval($_GET['household_id']);

    $query = "SELECT last_name, first_name, middle_name, birthdate, gender, relationship_to_head, occupation 
              FROM household_members 
              WHERE household_id = $household_id";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $members = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $members[] = $row;
        }

        echo json_encode([
            'status' => 200,
            'members' => $members,
        ]);
    } else {
        echo json_encode([
            'status' => 404,
            'message' => 'No household members found.',
        ]);
    }
} else {
    echo json_encode([
        'status' => 400,
        'message' => 'Invalid request. Household head ID is required.',
    ]);
}
?>
