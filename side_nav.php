<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="assets/images/brgylogo.png" alt="Barangay Logo" class="sidebar-logo">
            <h4 class="sidebar-title">Barangay System</h4>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="sidebar-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="user_management.php" class="sidebar-link">
                <i class="fas fa-user-cog"></i>
                <span>User Management</span>
            </a>
            <a href="resident_list.php" class="sidebar-link">
                <i class="fas fa-list"></i>
                <span>Masterlist</span>
            </a>
            <a href="add_resident.php" class="sidebar-link">
                <i class="fas fa-plus-circle"></i>
                <span>Add Resident</span>
            </a>
            <a href="generate_report.php" class="sidebar-link">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
            <a href="logout.php" class="sidebar-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>
</body>
</html>