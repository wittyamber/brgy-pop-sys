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
            <h4 class="sidebar-title">IBPMMS</h4>
        </div>
        <hr></hr>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="sidebar-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="user_management.php" class="sidebar-link">
                <i class="fas fa-user-cog"></i>
                <span>User Management</span>
            </a>
            <a href="household.php" class="sidebar-link">
                <i class="fas fa-house-user"></i>
                <span>Household</span>
            </a>
            <a href="household_members.php" class="sidebar-link">
                <i class="fas fa-users"></i>
                <span>Resident</span>
            </a>
            <a href="archive.php" class="sidebar-link">
                <i class="fas fa-archive"></i>
                <span>Archives Residents</span>
            </a>
            <a href="reports.php" class="sidebar-link">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
            <hr></hr>
            <a href="logout.php" class="sidebar-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>
</body>
</html>