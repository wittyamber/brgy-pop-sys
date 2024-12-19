<?php
    include 'side_nav.php';
    include 'config.php';

    $limit = 10; 
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
    $offset = ($page - 1) * $limit; 

    $count_query = "SELECT COUNT(*) AS total FROM barangay_officials WHERE status = 'Active'";
    $count_result = mysqli_query($conn, $count_query);
    $total_records = mysqli_fetch_assoc($count_result)['total'];

    $total_pages = ceil($total_records / $limit);

    $query = "SELECT name, position, date_assigned 
    FROM barangay_officials 
    WHERE status = 'Active' 
    LIMIT $limit OFFSET $offset";
    $barangay_officials = mysqli_query($conn, $query);


    $total_population = $conn->query(" 
        SELECT COUNT(DISTINCT hm.member_id) AS total
        FROM household_members hm
        INNER JOIN household h ON hm.household_id = h.household_id  
        WHERE hm.archived = 0 
    ")->fetch_assoc()['total'];

    $total_households = $conn->query("SELECT COUNT(*) AS total FROM household WHERE archived = 0 AND status = 'Active'")->fetch_assoc()['total'];

    $total_senior_citizens = (int) $conn->query(" 
        SELECT COALESCE(COUNT(*), 0) AS total 
        FROM household_members 
        WHERE archived = 0 
        AND TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= 60
    ")->fetch_assoc()['total'];

    $total_males = $conn->query("SELECT COUNT(*) AS total FROM household_members WHERE archived = 0 AND gender = 'Male'")->fetch_assoc()['total'];
    $total_females = $conn->query("SELECT COUNT(*) AS total FROM household_members WHERE archived = 0 AND gender = 'Female'")->fetch_assoc()['total'];
    $total_teens = $conn->query("SELECT COUNT(*) AS total FROM household_members WHERE archived = 0 AND TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 13 AND 19")->fetch_assoc()['total'];

    $pie_data_query = "
        SELECT p.purok_name AS purok, COUNT(hm.member_id) AS count
        FROM household_members hm
        INNER JOIN puroks p ON hm.purok_id = p.purok_id
        WHERE hm.archived = 0
        GROUP BY p.purok_name";
    $result = mysqli_query($conn, $pie_data_query);

    $pie_data = [];
    $most_populated_purok = null; 

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $pie_data[] = [
                'purok' => $row['purok'],
                'count' => $row['count']
            ];

            if ($most_populated_purok === null || $row['count'] > $most_populated_purok['count']) {
                $most_populated_purok = $row;
            }
        }
    }

    $pie_data_json = json_encode($pie_data);

    $household_purok_query = "
        SELECT p.purok_name AS purok, COUNT(h.household_id) AS count
        FROM household h
        INNER JOIN puroks p ON h.purok_id = p.purok_id
        WHERE h.archived = 0
        GROUP BY p.purok_name
    ";
    $household_purok_result = mysqli_query($conn, $household_purok_query);

    $household_purok_data = [];
    if ($household_purok_result) {
        while ($row = mysqli_fetch_assoc($household_purok_result)) {
            $household_purok_data[] = [
                'purok' => $row['purok'],
                'count' => $row['count']
            ];
        }
    }

    $household_purok_data_json = json_encode($household_purok_data);

    $barangay_officials_query = "SELECT name, position, date_assigned FROM barangay_officials WHERE status = 'Active'";
    $barangay_officials = mysqli_query($conn, $barangay_officials_query);

    if (!$barangay_officials) {
        die("Query failed: " . mysqli_error($conn));
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBPMMS | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Dashboard - Admin</h2>
        
        <!-- Summary Section -->
        <div class="row text-center">
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>Total Population</h5>
                        <p><?= $total_population; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>Total Households</h5>
                        <p><?= $total_households; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>Total Senior Citizens</h5>
                        <p><?= $total_senior_citizens; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>Total Males</h5>
                        <p><?= $total_males; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>Total Females</h5>
                        <p><?= $total_females; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>Most Populated Purok</h5>
                        <p>
                            <?php if ($most_populated_purok): ?>
                                <?= htmlspecialchars($most_populated_purok['purok']); ?> (<?= htmlspecialchars($most_populated_purok['count']); ?>)
                            <?php else: ?>
                                No data available
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Residents Pie Chart -->
        <div class="chart-container mb-4" style="max-width: 600px; margin: auto;">
            <canvas id="purokChart"></canvas>
        </div>

        <!-- Households Per Purok -->
        <div class="chart-container mb-4" style="max-width: 600px; margin: auto;">
            <canvas id="householdChart"></canvas>
        </div>

        <!-- Barangay Officials Section -->
        <div class="mt-5">
            <h3 class="text-center">Barangay Officials</h3>
            <button class="btn btn-primary mb-3" onclick="window.location.href='barangay_officials.php';">Manage Barangay Officials</button>
            <div class="table-responsive">
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
            </div>

            <!-- Pagination -->
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const colors = [
            '#007bff',  // Bootstrap primary blue
            '#28a745',  // Bootstrap green
            '#dc3545',  // Bootstrap red
            '#ffc107',  // Bootstrap yellow
            '#17a2b8',  // Bootstrap info
            '#6c757d'   // Bootstrap secondary gray
        ];

    function createBootstrapChart(elementId, data, title, xAxisLabel, yAxisLabel) {
        const ctx = document.getElementById(elementId).getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.purok),
                datasets: [{
                    label: title,
                    data: data.map(item => item.count),
                    backgroundColor: data.map((_, index) => colors[index % colors.length]),
                    borderColor: data.map((_, index) => colors[index % colors.length]),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: title,
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: { 
                            display: true, 
                            text: xAxisLabel,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: { 
                            display: true, 
                            text: yAxisLabel,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });
    }

    // Population Chart
    const pieData = <?= $pie_data_json; ?>;
    createBootstrapChart(
        'purokChart', 
        pieData, 
        'Population Count by Purok', 
        'Purok', 
        'Population Count'
    );

    // Household Chart
    const householdPurokData = <?= $household_purok_data_json; ?>;
    createBootstrapChart(
        'householdChart', 
        householdPurokData, 
        'Household Count by Purok', 
        'Purok', 
        'Household Count'
    );
    </script>
</body>
</html>