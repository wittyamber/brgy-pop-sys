<?php
    include ('../config.php');
    include 'side_navigation.php';

    session_start();
    if ($_SESSION['role'] !== 'Captain') {
        header("Location: ../index.php");
        exit();
    }

    // Fetch totals
    $total_population = $conn->query(" 
        SELECT COUNT(DISTINCT hm.member_id) AS total
        FROM household_members hm
        INNER JOIN households h ON hm.household_id = h.household_id  
        WHERE hm.archived = 0 AND h.archived = 0
    ")->fetch_assoc()['total'];

    $total_households = $conn->query("SELECT COUNT(*) AS total FROM households WHERE archived = 0")->fetch_assoc()['total'];

    $total_senior_citizens = (int) $conn->query("
        SELECT COALESCE(COUNT(*), 0) AS total 
        FROM household_members 
        WHERE archived = 0 
        AND TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= 60
    ")->fetch_assoc()['total'];

    $total_males = $conn->query("SELECT COUNT(*) AS total FROM household_members WHERE archived = 0 AND gender = 'Male'")->fetch_assoc()['total'];
    $total_females = $conn->query("SELECT COUNT(*) AS total FROM household_members WHERE archived = 0 AND gender = 'Female'")->fetch_assoc()['total'];
    $total_teens = $conn->query("SELECT COUNT(*) AS total FROM household_members WHERE archived = 0 AND TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 13 AND 19")->fetch_assoc()['total'];

    // Fetch most populated purok/address
    $pie_data_query = "
        SELECT address, COUNT(*) AS count 
        FROM households 
        WHERE archived = 0 
        GROUP BY address";
    $result = mysqli_query($conn, $pie_data_query);

    $pie_data = [];
    $most_populated_purok = null; // Initialize variable for most populated purok

    // Check if the query executed successfully
    if ($result) {
        // Fetch data and populate pie_data
        while ($row = mysqli_fetch_assoc($result)) {
            $pie_data[] = [
                'address' => $row['address'],
                'count' => $row['count']
            ];

            // Set the most populated purok based on highest count
            if ($most_populated_purok === null || $row['count'] > $most_populated_purok['count']) {
                $most_populated_purok = $row;
            }
        }
    } else {
        // Handle query error (optional)
        echo "Error executing query: " . mysqli_error($conn);
    }

    // Convert to JSON for JavaScript
    $pie_data_json = json_encode($pie_data);

    // Fetch barangay officials
    $barangay_officials = $conn->query("SELECT name, position, date_assigned FROM barangay_officials");

    // Pagination settings
    $limit = 5; // Number of rows per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Query for total records
    $total_query = "SELECT COUNT(*) AS total FROM barangay_officials";
    $total_result = mysqli_query($conn, $total_query);
    $total_row = mysqli_fetch_assoc($total_result);
    $total_records = $total_row['total'];

    // Query for paginated records
    $query = "SELECT name, position, date_assigned FROM barangay_officials LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $query);

    // Calculate total pages
    $total_pages = ceil($total_records / $limit);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Captain Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
    <div class="container mt-5">
        <!-- Summary Section -->
        <div class="row">
            <div class="summary-section">
                <div class="card summary-card text-center">
                    <div class="card-body">
                        <h5>Total Population</h5>
                        <p><?= $total_population; ?></p>
                    </div>
                </div>
            
                <div class="card summary-card text-center">
                    <div class="card-body">
                        <h5>Total Households</h5>
                        <p><?= $total_households; ?></p>
                    </div>
                </div>
            
                <div class="card summary-card text-center">
                    <div class="card-body">
                        <h5>Total Senior Citizens</h5>
                        <p><?= $total_senior_citizens; ?></p>
                    </div>
                </div>
                <div class="card summary-card text-center">
                    <div class="card-body">
                        <h5>Total Males</h5>
                        <p><?= $total_males; ?></p>
                    </div>
                </div>
                <div class="card summary-card text-center">
                    <div class="card-body">
                        <h5>Total Females</h5>
                        <p><?= $total_females; ?></p>
                    </div>
                </div>
                <div class="card summary-card text-center">
                    <div class="card-body">
                        <h5>Total Teens</h5>
                        <p><?= $total_teens; ?></p>
                    </div>
                </div>
    
                <div class="card summary-card text-center">
                    <div class="card-body">
                        <h5>Most Populated Purok</h5>
                        <?php if ($most_populated_purok && isset($most_populated_purok['address']) && isset($most_populated_purok['count'])): ?>
                            <p><?= htmlspecialchars($most_populated_purok['address']); ?> (<?= htmlspecialchars($most_populated_purok['count']); ?>)</p>
                        <?php else: ?>
                            <p>No data available for the most populated purok.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="chart-container" style="max-width: 400px; margin: auto;">
            <canvas id="purokChart"></canvas>
        </div>

        <!-- Barangay Officials Section -->
        <div class="mt-5">
            <h3 class="text-center">Barangay Officials</h3>
            <!-- <button class="btn btn-primary mb-3" onclick="window.location.href='barangay_officials.php';">Manage Barangay Officials</button> -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Date Assigned</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($official = $barangay_officials->fetch_assoc()): ?>
                    <tr>
                        <td><?= $official['name']; ?></td>
                        <td><?= $official['position']; ?></td>
                        <td><?= $official['date_assigned']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Pagination Links -->
            <nav>
                <ul   ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="#" data-page="<?= $page - 1; ?>">Previous</a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="#" data-page="<?= $i; ?>"><?= $i; ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="#" data-page="<?= $page + 1; ?>">Next</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>

        <!-- Include Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Fetch the data from the PHP variable
            const pieData = <?= $pie_data_json; ?>;  
            
            const labels = pieData.map(data => data.address);  
            const counts = pieData.map(data => data.count);  

            const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#7CEA9C', '#5B4E77', '#FF008C', '#FFDD00', '#808080', '#008000', '#E0FFFF', '#DC143C', '#708090', '#8B4513', '#FF69B4', '#8A2BE2', '#6495ED', '#2E8B57', '#FF6347', '#C0C0C0', '#800000', '#FFB6C1'];

            const dynamicColors = labels.map((label, index) => colors[index % colors.length]);

            const ctx = document.getElementById('purokChart').getContext('2d');

            const data = {
                labels: labels,  
                datasets: [{
                    data: counts,  
                    backgroundColor: dynamicColors,  
                }]
            };

            const config = {
                type: 'pie',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: true, 
                }
            };

            new Chart(ctx, config);


            //Pagination
            document.addEventListener('DOMContentLoaded', function () {
                const paginationLinks = document.querySelectorAll('.page-link');
                const officialsContainer = document.querySelector('#barangay-officials-container');

                paginationLinks.forEach(link => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault(); // Prevent page reload
                        const page = this.getAttribute('data-page'); // Get the page number

                        // Fetch data for the selected page
                        fetch(`captain_ui.php?page=${page}`)
                            .then(response => response.text())
                            .then(html => {
                                // Replace the content of the container
                                officialsContainer.innerHTML = html;

                                // Reinitialize the event listeners for the new links
                                const newLinks = document.querySelectorAll('.page-link');
                                newLinks.forEach(newLink => {
                                    newLink.addEventListener('click', function (e) {
                                        e.preventDefault();
                                        const newPage = this.getAttribute('data-page');
                                        fetchData(newPage);
                                    });
                                });
                            })
                            .catch(err => console.error('Error fetching data:', err));
                    });
                });

                function fetchData(page) {
                    fetch(`captain_ui.php?page=${page}`)
                        .then(response => response.text())
                        .then(html => {
                            officialsContainer.innerHTML = html;
                        })
                        .catch(err => console.error('Error:', err));
                }
            });

        </script>
    </div>
</body>
</html>
