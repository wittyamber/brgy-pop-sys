<?php
    require 'config.php';
    include 'side_nav.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBPMMS | Report</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>

    <link rel="stylesheet" href="css/reports.css">
</head>
<body>
<div class="container mt-5">
        <h1 class="text-center">Population Reports</h1>

        <!-- Report Type Selection -->
        <div class="form-group mt-4">
            <label for="reportType">Select Report Type:</label>
            <select id="reportType" class="form-control">
                <option value="quarterly">Quarterly Population</option>
                <option value="semi-annual">Semi-Annual Population</option>
                <option value="annual">Annual Population</option>
                <option value="age-distribution">Age Distribution</option>
                <option value="purok-population">Population by Purok</option>
                <option value="purok-households">Households by Purok</option>
            </select>
        </div>

        <!-- Table Container -->
        <div id="populationTableContainer" class="mt-4">
            <h4 class="text-center">Report Table</h4>
            <table id="populationTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <!-- Table headers will dynamically change -->
                    </tr>
                </thead>
                <tbody>
                    <!-- Rows will be dynamically populated -->
                </tbody>
            </table>
        </div>

        <!-- Line Chart Container -->
        <div id="lineChartContainer" class="mt-4" style="display: none;">
            <h4 class="text-center">Population Trend</h4>
            <canvas id="lineChart"></canvas>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const reportTypeSelect = document.getElementById('reportType');
            const populationTableContainer = document.getElementById('populationTableContainer');
            const populationTableHead = document.querySelector('#populationTable thead tr');
            const populationTableBody = document.querySelector('#populationTable tbody');
            const lineChartContainer = document.getElementById('lineChartContainer');
            const lineChartCanvas = document.getElementById('lineChart');
            let lineChart;

            // Function to fetch and display table data
            async function fetchTableData(reportType) {
                const response = await fetch(`generate_report.php?type=${reportType}`);
                const data = await response.json();

                // Clear table content
                populationTableHead.innerHTML = '';
                populationTableBody.innerHTML = '';

                // Handle different report types
                if (reportType === 'age-distribution') {
                    // Set table headers
                    populationTableHead.innerHTML = `
                        <th>Gender</th>
                        <th>Minors</th>
                        <th>Adults</th>
                    `;
                    // Populate table rows
                    data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row.Gender}</td>
                            <td>${row.Minors}</td>
                            <td>${row.Adults}</td>
                        `;
                        populationTableBody.appendChild(tr);
                    });
                } else if (reportType === 'purok-population' || reportType === 'purok-households') {
                    // Set table headers
                    populationTableHead.innerHTML = `
                        <th>Purok Name</th>
                        <th>Count</th>
                    `;
                    // Populate table rows
                    data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row['Purok Name']}</td>
                            <td>${row.Count}</td>
                        `;
                        populationTableBody.appendChild(tr);
                    });
                } else {
                    // Set table headers
                    populationTableHead.innerHTML = `
                        <th>Period</th>
                        <th>Population</th>
                    `;
                    // Populate table rows
                    data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row.Period}</td>
                            <td>${row.Population}</td>
                        `;
                        populationTableBody.appendChild(tr);
                    });
                }

                // Hide chart container for non-population trend types
                lineChartContainer.style.display = 'none';
            }

            // Function to fetch and display line chart data
            async function fetchLineChartData(reportType) {
                const response = await fetch(`generate_report.php?type=${reportType}`);
                const data = await response.json();

                const labels = data.map(row => row.Period);
                const values = data.map(row => row.Population);

                // Destroy existing chart instance
                if (lineChart) {
                    lineChart.destroy();
                }

                // Create new line chart
                lineChart = new Chart(lineChartCanvas, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Population',
                            data: values,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: { title: { display: true, text: 'Period' } },
                            y: { title: { display: true, text: 'Population' } }
                        }
                    }
                });

                // Hide table and show chart container
                populationTableContainer.style.display = 'none';
                lineChartContainer.style.display = 'block';
            }

            // Event listener for report type selection
            reportTypeSelect.addEventListener('change', function () {
                const selectedType = reportTypeSelect.value;

                if (selectedType === 'quarterly' || selectedType === 'semi-annual' || selectedType === 'annual') {
                    fetchLineChartData(selectedType); // Show line chart
                } else {
                    fetchTableData(selectedType); // Show table
                    populationTableContainer.style.display = 'block';
                }
            });

            // Load default report type on page load
            fetchLineChartData('annual');
        });
    </script>
</body>
</html>
