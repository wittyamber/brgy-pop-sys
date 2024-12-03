<?php
    // Include database connection
    include 'config.php';

    //Total Population
    $total_population = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM households WHERE archived = '0'"))['total'];
    $total_population = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM household_members WHERE archived = '0'"))['total'];

    $total_purok = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT address) AS total FROM households"))['total'];

    $most_populated_purok = mysqli_fetch_assoc(mysqli_query($conn, "SELECT address, COUNT(*) AS count FROM households GROUP BY address ORDER BY count DESC LIMIT 1"))['address'];

    $senior_citizens = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM households WHERE age >= 60 "))['total'];

    $total_males = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM households WHERE gender = 'Male'"))['total'];

    $total_females = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM households WHERE gender = 'Female'"))['total'];

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
        <?php include 'side_nav.php'; ?>

        <!-- Main Dashboard Content -->
        <div class="main-content">
            <h1>Welcome, Admin!</h1>
            <div class="row mt-4">
                <div class="col-md-3 mb-3">
                    <div class="card text-white" style="background: radial-gradient(ellipse at center, rgb(255,145,12) 1%,rgb(255,175,75) 50%,rgb(255,171,76) 50%,rgb(255,196,130) 100%);">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-container">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="card-content">
                                <h5 class="card-title">Total Population</h5>
                                <p class="card-text display-6"><?php echo $total_population; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-white" style="background: radial-gradient(ellipse at center, rgb(37,226,247) 0%,rgb(133,231,234) 47%,rgb(191,234,239) 100%);">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-container">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="card-content">
                                <h5 class="card-title">Total Purok</h5>
                                <p class="card-text display-6"><?php echo $total_purok; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-white" style="background: radial-gradient(ellipse at center, rgb(255,187,0) 0%,rgb(247,219,81) 49%,rgb(249,230,179) 100%);">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-container">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="card-content">
                                <h5 class="card-title">Most Populated Purok</h5>
                                <p class="card-text display-6"><?php echo $most_populated_purok; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-white" style="background: radial-gradient(ellipse at center, rgb(95,193,3) 0%,rgb(126,204,81) 51%,rgb(159,201,134) 100%);">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-container">
                                <i class="fas fa-user-alt"></i>
                            </div>
                            <div class="card-content">
                                <h5 class="card-title">Senior Citizens</h5>
                                <p class="card-text display-6"><?php echo $senior_citizens; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-white" style="background: radial-gradient(ellipse at center, rgb(33,107,255) 0%,rgb(69,154,224) 48%,rgb(91,146,255) 48%,rgb(165,195,255) 100%);">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-container">
                                <i class="fas fa-male"></i>
                            </div>
                            <div class="card-content">
                                <h5 class="card-title">Total Males</h5>
                                <p class="card-text display-6"><?php echo $total_males; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-white" style="background: radial-gradient(ellipse at center, rgb(255,18,10) 0%,rgb(255,86,71) 47%,rgb(255,150,142) 100%);">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-container">
                                <i class="fas fa-female"></i>
                            </div>
                            <div class="card-content">
                                <h5 class="card-title">Total Females</h5>
                                <p class="card-text display-6"><?php echo $total_females; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
