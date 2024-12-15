<?php
    // Include the database connection
    include 'config.php';

    // Check if 'official_id' is provided in the GET request
    if (isset($_GET['official_id']) && !empty($_GET['official_id'])) {
        $officialId = $_GET['official_id'];

        // Prepare the SQL query
        $query = "SELECT * FROM barangay_officials WHERE official_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            // Bind the parameter and execute the query
            $stmt->bind_param("i", $officialId); // Assuming official_id is an integer
            $stmt->execute();

            // Get the result
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Fetch and return the data as JSON
                echo json_encode($result->fetch_assoc());
            } else {
                // Handle case where the official is not found
                echo json_encode(["error" => "Official not found"]);
            }

            // Close the statement
            $stmt->close();
        } else {
            // Handle case where the query could not be prepared
            echo json_encode(["error" => "Failed to prepare the query"]);
        }
    } else {
        // Handle missing or empty official_id
        echo json_encode(["error" => "Invalid official_id"]);
    }

    // Close the database connection
    $conn->close();
?>
