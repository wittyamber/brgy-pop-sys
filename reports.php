<?php
    include 'side_nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBPMMS | Report</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <link rel="stylesheet" href="css/reports.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Barangay Population Report</h1>
            <div class="date-filter">
                <select id="year">
                    <?php
                    $currentYear = date('Y');
                    for($i = $currentYear; $i >= $currentYear - 5; $i--) {
                        echo "<option value='$i'>$i</option>";
                    }
                    ?>
                </select>
                <select id="month">
                    <option value="">All Months</option>
                    <?php
                    $months = [
                        1 => 'January', 2 => 'February', 3 => 'March',
                        4 => 'April', 5 => 'May', 6 => 'June',
                        7 => 'July', 8 => 'August', 9 => 'September',
                        10 => 'October', 11 => 'November', 12 => 'December'
                    ];
                    foreach($months as $num => $name) {
                        echo "<option value='$num'>$name</option>";
                    }
                    ?>
                </select>
                <button onclick="updateReport()">Generate Report</button>
            </div>
        </div>

        <button class="export-btn" onclick="exportToExcel()">
            <i class="fas fa-download"></i> Export to Excel
        </button>

        <div class="stats-container">
            <div class="stat-card">
                <h3>Total Population</h3>
                <div class="number"></div>
            </div>
            <div class="stat-card">
                <h3>Male Population</h3>
                <div class="number"></div>
            </div>
            <div class="stat-card">
                <h3>Female Population</h3>
                <div class="number"></div>
            </div>
            <div class="stat-card">
                <h3>Seniors Populations</h3>
                <div class="number"></div>
            </div>
            <div class="stat-card">
                <h3>Households</h3>
                <div class="number"></div>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="populationChart"></canvas>
        </div>

        <div class="chart-container">
            <canvas id="ageDistributionChart"></canvas>
        </div>

        <div class="data-table">
            <h2>Detailed Population Data</h2>
            <table>
                <thead>
                    <tr>
                        <th>Age Group</th>
                        <th>Male</th>
                        <th>Female</th>
                        <th>Seniors</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $ageGroups = [
                        '0-4', '5-9', '10-14', '15-19', '20-24',
                        '25-29', '30-34', '35-39', '40-44', '45-49',
                        '50-54', '55-59', '60-64', '65+'
                    ];
                    foreach($ageGroups as $group) {
                        $male = rand(0, 0);
                        $female = rand(0, 0);
                        $seniors = rand(0, 0);
                        $total = $male + $female + $seniors;
                        echo "<tr>
                                <td>$group years</td>
                                <td>$male</td>
                                <td>$female</td>
                                 <td>$seniors</td>
                                <td>$total</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Population Chart
        const populationCtx = document.getElementById('populationChart').getContext('2d');
        new Chart(populationCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Population Growth',
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    borderColor: '#007bff',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Population Growth Trend'
                    }
                }
            }
        });

        // Age Distribution Chart
        const ageCtx = document.getElementById('ageDistributionChart').getContext('2d');
        new Chart(ageCtx, {
            type: 'bar',
            data: {
                labels: ['0-4', '5-9', '10-14', '15-19', '20-24', '25-29', '30-34', '35-39', '40-44', '45-49', '50-54', '55-59', '60-64', '65+'],
                datasets: [{
                    label: 'Male',
                    data: [0, 0, 0, 0, 0,0, 0, 0, 0, 0, 0, 0, 0, 0],
                    backgroundColor: '#007bff'
                }, {
                    label: 'Female',
                    data: [0, 0, 0, 0, 0,0, 0, 0, 0, 0, 0, 0, 0, 0],
                    backgroundColor: '#dc3545'
                }, {
                    label: 'Seniors',
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    backgroundColor: '#CDC1FF'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Population by Age Group and Gender'
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true
                    }
                }
            }
        });

        function updateReport() {
            const year = document.getElementById('year').value;
            const month = document.getElementById('month').value;
            // Add AJAX call to update report data
            console.log(`Updating report for ${month}/${year}`);
        }

        function exportToExcel() {
            // Add Excel export functionality
            console.log('Exporting to Excel...');
        }
    </script>
</body>
</html>
