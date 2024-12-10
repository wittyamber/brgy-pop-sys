<?php
    header('Content-Type: application/json');
    include 'config.php';

    $month = $_GET['month'];
    $year = $_GET['year'];

    $conn = new mysqli("localhost", "root", "", "brgy_pop_sys");
    if ($conn->connect_error) {
        die(json_encode(["error" => "Database connection failed"]));
    }

    // Fetch population data
    $query = $conn->prepare("
        SELECT 
            hh.address,
            hm.gender,
            YEAR(CURDATE()) - YEAR(hm.birthdate) AS age,
            COUNT(*) AS total
        FROM 
            households AS hh
        JOIN 
            household_members AS hm 
        ON 
            hh.household_id = hm.household_id
        WHERE 
            MONTH(hh.created_at) = ? AND YEAR(hh.created_at) = ?
        GROUP BY 
            hh.address, hm.gender, age
    ");
    $query->bind_param("ii", $month, $year);
    $query->execute();
    $result = $query->get_result();

    $data = ["graph" => ["labels" => [], "values" => []], "table" => []];
    while ($row = $result->fetch_assoc()) {
        $ageBracket = floor($row['age'] / 10) * 10 . "-" . (floor($row['age'] / 10) * 10 + 9);
        $data['table'][] = [
            "ageBracket" => $ageBracket,
            "males" => $row['gender'] === "Male" ? $row['total'] : 0,
            "females" => $row['gender'] === "Female" ? $row['total'] : 0,
            "total" => $row['total']
        ];
        $data['graph']['labels'][] = $row['address'];
        $data['graph']['values'][] = $row['total'];
    }

    echo json_encode($data);
    $conn->close();
?>
