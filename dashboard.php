<?php
    include 'side_nav.php';
    include 'config.php';

    // Fetch totals
    $total_population = $conn->query(" 
        SELECT COUNT(DISTINCT hm.member_id) AS total
        FROM household_members hm
        INNER JOIN household h ON hm.household_id = h.household_id  
        WHERE hm.archived = 0 AND h.archived = 0
    ")->fetch_assoc()['total'];

    $total_households = $conn->query("SELECT COUNT(*) AS total FROM household WHERE archived = 0")->fetch_assoc()['total'];

    $total_senior_citizens = (int) $conn->query(" 
        SELECT COALESCE(COUNT(*), 0) AS total 
        FROM household_members 
        WHERE archived = 0 
        AND TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= 60
    ")->fetch_assoc()['total'];

    $total_males = $conn->query("SELECT COUNT(*) AS total FROM household_members WHERE archived = 0 AND gender = 'Male'")->fetch_assoc()['total'];
    $total_females = $conn->query("SELECT COUNT(*) AS total FROM household_members WHERE archived = 0 AND gender = 'Female'")->fetch_assoc()['total'];
    $total_teens = $conn->query("SELECT COUNT(*) AS total FROM household_members WHERE archived = 0 AND TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 13 AND 19")->fetch_assoc()['total'];

    // Fetch most populated purok
    $pie_data_query = "
        SELECT p.purok_name AS purok, COUNT(hm.member_id) AS count
        FROM household_members hm
        INNER JOIN puroks p ON hm.purok_id = p.purok_id
        WHERE hm.archived = 0
        GROUP BY p.purok_name";
    $result = mysqli_query($conn, $pie_data_query);

    $pie_data = [];
    $most_populated_purok = null; // Initialize variable for most populated purok

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

    // Convert to JSON for JavaScript
    $pie_data_json = json_encode($pie_data);

    // Fetch barangay officials
    $barangay_officials = $conn->query("SELECT name, position, date_assigned FROM barangay_officials");

    // Pagination settings
    $limit = 5; 
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    $total_query = "SELECT COUNT(*) AS total FROM barangay_officials";
    $total_result = mysqli_query($conn, $total_query);
    $total_row = mysqli_fetch_assoc($total_result);
    $total_records = $total_row['total'];

    $query = "SELECT name, position, date_assigned FROM barangay_officials LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $query);
    $total_pages = ceil($total_records / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBPMMS | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        <!-- Pie Chart -->
        <div class="chart-container mb-4" style="max-width: 600px; margin: auto;">
            <canvas id="purokChart"></canvas>
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
            <!-- Pagination Links -->
            <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="dashboard.php?page=<?= $page - 1; ?>">Previous</a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="dashboard.php?page=<?= $i; ?>"><?= $i; ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="dashboard.php?page=<?= $page + 1; ?>">Next</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const pieData = <?= $pie_data_json; ?>;
        const labels = pieData.map(data => data.purok);
        const counts = pieData.map(data => data.count);

        const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#7CEA9C', '#5B4E77', '#FF008C'];
        const dynamicColors = labels.map((label, index) => colors[index % colors.length]);

        const ctx = document.getElementById('purokChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Population Count by Purok',
                    data: counts,
                    backgroundColor: dynamicColors,
                    borderColor: dynamicColors.map(color => color),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: { display: true, text: 'Purok' }
                    },
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Population Count' }
                    }
                }
            }
        });
    </script>
</body>
</html>