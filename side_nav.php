<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                display: flex;
            }
            .sidebar {
                min-width: 250px;
                max-width: 250px;
                background-color: #343a40;
                color: white;
                height: 100vh;
                padding-top: 20px;
            }
            .sidebar a {
                color: white;
                padding: 10px;
                display: block;
                text-decoration: none;
            }
            .sidebar a:hover {
                background-color: #495057;
            }
            .main-content {
                flex: 1;
                padding: 20px;
            }
        </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <h4 class="text-center">Barangay System</h4>
        <a href="dashboard.php">Dashboard</a>
        <a href="user_management.php">User Management</a>
        <a href="resident_list.php">Masterlist</a>
        <a href="add_resident.php">Add Resident</a>
        <a href="generate_report.php">Reports</a>
        <a href="logout.php">Logout</a>
    </div>
    
</body>
</html>