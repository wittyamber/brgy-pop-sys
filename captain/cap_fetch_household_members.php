<?php
include '../config.php';

if (isset($_POST['household_id'])) {
    $household_id = $_POST['household_id'];
    $query = "SELECT CONCAT(first_name, ' ', last_name) AS name, relationship_to_head 
              FROM household_members 
              WHERE household_id = $household_id AND archived = 0";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['name']}</td>
                    <td>{$row['relationship_to_head']}</td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='3' class='text-center'>No active members found.</td></tr>";
    }
}
?>
