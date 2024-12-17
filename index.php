<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBPMMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="assets/images/brgylogo.png" alt="Barangay Logo">
                IBPMMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content text-center text-white">
            <h1>Integrated Barangay Population Management System</h1>
            <a href="#" class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#loginModal">Get Started</a>
        </div>
    </div>

    <!-- About Section -->
    <div id="about" class="about-section">
        <div class="container">
            <h2 class="text-center mb-4">About IBPMMS</h2>
            <div class="row">
                <div class="col-md-6">
                    <h4>Our Mission</h4>
                    <p>To provide a cutting-edge, user-focused system designed to manage population data in an efficient and precise manner. By giving barangay officials reliable tools, we aim to enhance the delivery of services, improve transparency, and support data-driven decision-making for the betterment of the community.</p>
                </div>
                <div class="col-md-6">
                <h4>Key Features</h4>
                <ul>
                    <li>Easy Data Management</li>
                    <li>Purok-Based Monitoring</li>
                    <li>Reporting and Analytics</li>
                </ul>
            </div>
        </div>

        <hr></hr>
        
        <div class="row mt-5">
            <h2 class="text-center mb-4">Barangay Zone III</h2>
            <div class="col-md-6">
                <h4>Barangay Zone III Vision</h4>
                <p>Five years from now, BARANGAY ZONE III will become the Commercial & Institutional Center of the Municipality with a clean & peaceful environment through a God-Centered, Gender-Sensitive, Healthy and Empowered people and a strong, transparent and accountable governance.
                </p>
            </div>
            <div class="col-md-6">
                <h4>Barangay Zone III Mission</h4>
                <p>The Barangay Government Unit of Zone III shall have component, committed, honest, compassionate and yard-working Barangay Officials effective and efficient in the delivery of Quality Health, Education, Environment, Infrastructure, Sports and Cultural Development Program and basic Social Services Programs through a armstrong partnership between and among the constituents, officials and stakeholders of the Barangay.
                </p>
            </div>
        </div>
    </div>
</div>

    <!-- Login Modal -->
    <div class="modal fade login-modal" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="assets/images/brgylogo.png" alt="Barangay Logo" class="modal-logo">
                    <form action="login.php" method="POST">
                        <div class="form-group mb-3">
                            <label for="username">Username</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" name="username" id="username" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
