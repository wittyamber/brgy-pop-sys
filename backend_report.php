<?php
    include 'config.php';

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header('Content-Type: application/json');

    // Get year and month from GET request
    $year = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : date('Y');
    $month = isset($_GET['month']) && is_numeric($_GET['month']) ? $_GET['month'] : '';

    // Fetch distinct years for dropdown
    $yearsQuery = "SELECT DISTINCT YEAR(created_at) AS year FROM household_members ORDER BY year DESC";
    $yearsResult = $conn->query($yearsQuery);

    if (!$yearsResult) {
        error_log('Error fetching years: ' . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Error fetching years']);
        exit;
    }

    $years = [];
    while ($row = $yearsResult->fetch_assoc()) {
        $years[] = $row['year'];
    }

    // Query for population growth and detailed data
    $populationQuery = "
        SELECT 
            hh.address,
            COUNT(hm.id) AS total_population,
            SUM(CASE WHEN hm.gender = 'Male' THEN 1 ELSE 0 END) AS total_males,
            SUM(CASE WHEN hm.gender = 'Female' THEN 1 ELSE 0 END) AS total_females,
            CASE
                WHEN TIMESTAMPDIFF(YEAR, hm.birthdate, CURDATE()) BETWEEN 0 AND 10 THEN '0-10'
                WHEN TIMESTAMPDIFF(YEAR, hm.birthdate, CURDATE()) BETWEEN 11 AND 20 THEN '11-20'
                WHEN TIMESTAMPDIFF(YEAR, hm.birthdate, CURDATE()) BETWEEN 21 AND 30 THEN '21-30'
                WHEN TIMESTAMPDIFF(YEAR, hm.birthdate, CURDATE()) BETWEEN 31 AND 40 THEN '31-40'
                WHEN TIMESTAMPDIFF(YEAR, hm.birthdate, CURDATE()) BETWEEN 41 AND 50 THEN '41-50'
                WHEN TIMESTAMPDIFF(YEAR, hm.birthdate, CURDATE()) BETWEEN 51 AND 60 THEN '51-60'
                ELSE '61+'
            END AS age_group
        FROM households hh
        INNER JOIN household_members hm ON hh.household_id = hm.household_id
        WHERE hm.archived = 0
            AND YEAR(hh.created_at) = ?
            " . ($month ? "AND MONTH(hh.created_at) = ?" : "") . "
        GROUP BY hh.address, age_group
        ORDER BY hh.address";

    $stmt = $conn->prepare($populationQuery);

    if (!$stmt) {
        error_log('Error preparing statement: ' . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Database query error']);
        exit;
    }

    // Bind parameters based on the presence of month
    if ($month) {
        $stmt->bind_param('ii', $year, $month);
    } else {
        $stmt->bind_param('i', $year);
    }

    if (!$stmt->execute()) {
        error_log('Error executing statement: ' . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Error executing database query']);
        exit;
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        error_log('No data found for year: ' . $year . ', month: ' . $month);
        echo json_encode(['success' => false, 'message' => 'No data found']);
        exit;
    }

    // Fetch data
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Return response as JSON
    echo json_encode(['success' => true, 'data' => $data, 'years' => $years]);
?>
