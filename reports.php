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
    <div class="container my-5">
        <h1 class="text-center">Population Growth Report</h1>

        <!-- Filters -->
        <div class="row my-3">
            <div class="col-md-6">
                <label for="month" class="form-label">Select Month</label>
                <select id="month" class="form-select">
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
            <div class="col-md-6">
                <label for="year" class="form-label">Select Year</label>
                <select id="year" class="form-select">
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                </select>
            </div>
        </div>

        <!-- Line Graph -->
        <canvas id="populationGraph" height="100"></canvas>

        <!-- Data Table -->
        <h3 class="mt-5">Detailed Data</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Age Distribution</th>
                    <th>Males</th>
                    <th>Females</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody id="reportTableBody">
                <!-- Dynamically populated rows -->
            </tbody>
        </table>

        <!-- Print Button -->
        <button id="printButton" class="btn btn-primary">Print Report</button>
    </div>


    <script>
        document.getElementById("month").addEventListener("change", generateReport);
            document.getElementById("year").addEventListener("change", generateReport);

            const ctx = document.getElementById("populationGraph").getContext("2d");
            let chart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: [],
                    datasets: [{
                        label: "Population Growth",
                        data: [],
                        borderColor: "blue",
                        fill: false
                    }]
                }
            });

            function generateReport() {
                const month = document.getElementById("month").value;
                const year = document.getElementById("year").value;

                fetch(`backend_report.php?month=${month}&year=${year}`)
                    .then(response => response.json())
                    .then(data => {
                        // Update Graph
                        chart.data.labels = data.graph.labels;
                        chart.data.datasets[0].data = data.graph.values;
                        chart.update();

                        // Update Table
                        const tbody = document.getElementById("reportTableBody");
                        tbody.innerHTML = "";
                        data.table.forEach(row => {
                            tbody.innerHTML += `
                                <tr>
                                    <td>${row.ageBracket}</td>
                                    <td>${row.males}</td>
                                    <td>${row.females}</td>
                                    <td>${row.total}</td>
                                </tr>
                            `;
                        });
                    });
            }

            // Print functionality
            document.getElementById("printButton").addEventListener("click", () => {
                window.print();
            });
    </script>
</body>
</html>
