<?php
    include 'config.php'; 
    include 'side_nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBPMMS | Report</title>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <link rel="stylesheet" href="css/reports.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Barangay Population Report</h1>
        <div class="row my-3">
            <div class="col-md-3">
                <select id="year" class="form-select">
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="month" class="form-select">
                    <option value="">All Months</option>
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
            </div>
        </div>

        <canvas id="lineChart"></canvas>
        <div id="reportTable" class="mt-5"></div>

        <button onclick="window.print()" class="btn btn-success">Print Report</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Add change event listeners to year and month dropdowns
            const yearSelect = document.getElementById('year');
            const monthSelect = document.getElementById('month');

            yearSelect.addEventListener('change', generateReport);
            monthSelect.addEventListener('change', generateReport);
        });

        function generateReport() {
            const year = document.getElementById('year').value;
            const month = document.getElementById('month').value;

            // Debug values of year and month
            console.log('Fetching report for Year:', year, 'Month:', month);

            fetch(`backend_report.php?year=${year}&month=${month}`)
                .then(response => {
                    console.log('Response Status:', response.status); // Debug HTTP status
                    if (!response.ok) {
                        throw new Error(`HTTP Error: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data); // Debug response data
                    if (data.success) {
                        updateChart(data.data); // Update the chart
                        updateTable(data.data); // Update the table
                    } else {
                        alert('Failed to generate report');
                    }
                })
                .catch(error => {
                    console.error('Error:', error); // Debug any errors
                    alert('An error occurred while generating the report. Please try again.');
                });
        }

        const ctx = document.getElementById('lineChart').getContext('2d');
        let lineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [], // Dynamic labels
                datasets: [
                    {
                        label: 'Population Growth',
                        data: [], // Dynamic data
                        borderColor: 'blue',
                        fill: false,
                    }
                ]
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

        function updateChart(data) {
            lineChart.data.labels = data.labels; // Assuming data.labels is an array of labels
            lineChart.data.datasets[0].data = data.populationGrowth; // Assuming data.populationGrowth is an array
            lineChart.update();
        }

        function updateTable(data) {
            let html = `
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Address</th>
                            <th>Age Group</th>
                            <th>Males</th>
                            <th>Females</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>`;
            data.forEach(row => {
                html += `
                    <tr>
                        <td>${row.address}</td>
                        <td>${row.age_group}</td>
                        <td>${row.total_males}</td>
                        <td>${row.total_females}</td>
                        <td>${row.total_population}</td>
                    </tr>`;
            });
            html += '</tbody></table>';
            document.getElementById('reportTable').innerHTML = html;
        }
    </script>
</body>
</html>
