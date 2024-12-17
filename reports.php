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
    <div class="container mt-4">
        <h2 class="text-center">Generate Reports</h2>
        <form id="reportForm">
            <div class="form-group">
                <label for="reportType">Select Report Type:</label>
                <select id="reportType" class="form-control">
                    <option value="quarterly">Quarterly Population</option>
                    <option value="semi-annual">Semi-Annual Population</option>
                    <option value="annual">Annual Population</option>
                </select>
            </div>
        </form>

        <!-- Population Chart -->
        <div id="chartContainer" class="mt-4">
            <canvas id="populationChart"></canvas>
        </div>

        <!-- Age Distribution Chart -->
        <div id="ageDistributionContainer" class="mt-4">
            <h4 class="text-center">Age Distribution by Gender</h4>
            <canvas id="ageDistributionChart"></canvas>
        </div>

        <!-- Purok Population Distribution Chart -->
        <div id="purokPopulationContainer" class="mt-4">
            <h4 class="text-center">Population Distribution per Purok</h4>
            <canvas id="purokPopulationChart"></canvas>
        </div>

        <!-- Purok Household Distribution Chart -->
        <div id="purokHouseholdContainer" class="mt-4">
            <h4 class="text-center">Household Distribution per Purok</h4>
            <canvas id="purokHouseholdChart"></canvas>
        </div>

        <div id="printSection" class="mt-3">
            <button class="btn btn-primary" onclick="window.print()">Print Report</button>
        </div>
    </div>

    

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const reportTypeSelect = document.getElementById('reportType');
            const populationChartCanvas = document.getElementById('populationChart').getContext('2d');
            const ageDistributionCanvas = document.getElementById('ageDistributionChart').getContext('2d');
            const purokPopulationCanvas = document.getElementById('purokPopulationChart').getContext('2d');
            const purokHouseholdCanvas = document.getElementById('purokHouseholdChart').getContext('2d');

            let populationChart, ageChart, purokPopChart, purokHouseChart;

            function generateChart(canvas, data, label, type = 'bar') {
                return new Chart(canvas, {
                    type: type,
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: label,
                            data: data.values,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: label
                            }
                        }
                    }
                });
            }

            async function fetchData(reportType, endpoint, chartInstance, canvas, label) {
                const response = await fetch(`generate_report.php?type=${endpoint}`);
                const data = await response.json();
                if (chartInstance) chartInstance.destroy();
                chartInstance = generateChart(canvas, data, label);
                return chartInstance;
            }

            async function loadAllCharts(reportType) {
                populationChart = await fetchData(reportType, 'population', populationChart, populationChartCanvas, `${reportType} Population Report`);
                ageChart = await fetchData('age-distribution', 'age-distribution', ageChart, ageDistributionCanvas, 'Age Distribution by Gender');
                purokPopChart = await fetchData('purok-population', 'purok-population', purokPopChart, purokPopulationCanvas, 'Population per Purok');
                purokHouseChart = await fetchData('purok-households', 'purok-households', purokHouseChart, purokHouseholdCanvas, 'Households per Purok');
            }

            reportTypeSelect.addEventListener('change', function () {
                const selectedType = reportTypeSelect.value;
                loadAllCharts(selectedType);
            });

            // Load default charts on page load
            loadAllCharts(reportTypeSelect.value);
        });
    </script>
</body>
</html>
