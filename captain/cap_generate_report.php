<?php
    require '../config.php'; // Database connection

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['type'])) {
        $type = $_GET['type'];
        $tableData = []; 

        if ($type === 'quarterly') {
            $query = "SELECT QUARTER(created_at) AS period, COUNT(*) AS population
                      FROM household_members 
                      GROUP BY period";
        } elseif ($type === 'semi-annual') {
            $query = "SELECT CEIL(MONTH(created_at) / 6) AS period, COUNT(*) AS population
                      FROM household_members 
                      GROUP BY period";
        } elseif ($type === 'annual') {
            $query = "SELECT YEAR(created_at) AS period, COUNT(*) AS population
                      FROM household_members 
                      GROUP BY period";
        } elseif ($type === 'age-distribution') {
            $query = "SELECT gender, 
                             SUM(CASE WHEN age BETWEEN 0 AND 18 THEN 1 ELSE 0 END) AS minors, 
                             SUM(CASE WHEN age > 18 THEN 1 ELSE 0 END) AS adults 
                      FROM household_members 
                      GROUP BY gender";
        } elseif ($type === 'purok-population') {
            $query = "SELECT p.purok_name, COUNT(hm.member_id) AS total_population 
                      FROM household_members hm 
                      INNER JOIN puroks p ON hm.purok_id = p.purok_id
                      WHERE hm.archived = 0
                      GROUP BY p.purok_name";
        } elseif ($type === 'purok-households') {
            $query = "SELECT p.purok_name, COUNT(h.household_id) AS total_households 
                      FROM household h
                      INNER JOIN puroks p ON h.purok_id = p.purok_id
                      WHERE h.archived = 0
                      GROUP BY p.purok_name";
        }

        $result = $conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                if ($type === 'age-distribution') {
                    $tableData[] = [
                        'Gender' => ucfirst($row['gender']),
                        'Minors' => $row['minors'],
                        'Adults' => $row['adults']
                    ];
                } elseif ($type === 'purok-population' || $type === 'purok-households') {
                    $tableData[] = [
                        'Purok Name' => $row['purok_name'],
                        'Count' => $row['total_population'] ?? $row['total_households']
                    ];
                } else {
                    $tableData[] = [
                        'Period' => $row['period'],
                        'Population' => $row['population']
                    ];
                }
            }
        }

        echo json_encode($tableData);
        exit();
    }
?>
