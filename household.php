<?php
    require 'config.php';
    include 'side_nav.php';
    include 'includes/alerts.php';

    // Start session if not started already
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Pagination settings
    $limit = 10; // Records per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($page - 1) * $limit;

    // Search query
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    // Base SQL query
    $sql = "SELECT * FROM households WHERE archived = 0";
    $params = [];
    $types = "";

    // If a search term is provided, add search filters
    if (!empty($search)) {
        $sql .= " AND (
            last_name LIKE ? OR 
            first_name LIKE ? OR 
            middle_name LIKE ? OR 
            address LIKE ? OR 
            contact_number LIKE ?
        )";
        $searchTerm = '%' . $search . '%';
        $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm];
        $types = "sssss";
    }

    // Add pagination to the query
    $sql .= " ORDER BY last_name ASC LIMIT ?, ?";
    $params[] = $start;
    $params[] = $limit;
    $types .= "ii";

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL error: " . $conn->error);
    }

    // Bind parameters dynamically
    $stmt->bind_param($types, ...$params);

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch active households
    $households = $result->fetch_all(MYSQLI_ASSOC);

    // Total count for pagination
    $countSql = "SELECT COUNT(*) AS total FROM households WHERE archived = 0";
    if (!empty($search)) {
        $countSql .= " AND (
            last_name LIKE ? OR 
            first_name LIKE ? OR 
            middle_name LIKE ? OR 
            address LIKE ? OR 
            contact_number LIKE ?
        )";
        $countStmt = $conn->prepare($countSql);
        $countStmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $total_rows = $countResult->fetch_assoc()['total'];
    } else {
        $countResult = $conn->query($countSql);
        $total_rows = $countResult->fetch_assoc()['total'];
    }

    $total_pages = ceil($total_rows / $limit);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBPMMS | Household </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/household.css">
</head>
<body>
    <div class="container mt-5">

        <!-- Household Heads Section -->
        <h2 class="text-center">Household Head</h2>

        <!-- Search Bar and Add Button -->
        <div class="d-flex justify-content-center align-items-center my-4">

            <form class="search-form" method="GET" action="household.php">
                <div class="input-group">
                <input 
                    class="form-control me-2" 
                    type="text" 
                    name="search" 
                    placeholder="Search by name, address, etc." 
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                />
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
                </div>
            </form>

            <button class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-user-plus"></i> Add Household Head
            </button>
        </div>

        <!-- Table and Edit and Archive Buttons -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Birthdate</th>
                    <th>Age</th>
                    <th>Civil Status</th>
                    <th>Gender</th>
                    <th>Tribe</th>
                    <th>Occupation</th>
                    <th>Address</th>
                    <th>Contact Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($households)): ?>
                    <?php foreach ($households as $index => $household): ?>
                        <tr>
                            <td><?= ($start + $index + 1); ?></td>
                            <td><?= $household['last_name'] . ', ' . $household['first_name'] . ' ' . $household['middle_name']; ?></td>
                            <td><?= $household['birthdate']; ?></td>
                            <td><?= $household['age']; ?></td>
                            <td><?= $household['civil_status']; ?></td>
                            <td><?= $household['gender']; ?></td>
                            <td><?= $household['tribe']; ?></td>
                            <td><?= $household['occupation']; ?></td>
                            <td><?= $household['address']; ?></td>
                            <td><?= $household['contact_number']; ?></td>
                            <td>
                                
                            <!-- Edit Button-->
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal" 
                                data-id="<?= $household['household_id']; ?>" 
                                data-last-name="<?= $household['last_name']; ?>"
                                data-first-name="<?= $household['first_name']; ?>"
                                data-middle-name="<?= $household['middle_name']; ?>"
                                data-gender="<?= $household['gender']; ?>"
                                data-civil-status="<?= $household['civil_status']; ?>"
                                data-tribe="<?= $household['tribe']; ?>"
                                data-occupation="<?= $household['occupation']; ?>"
                                data-address="<?= $household['address']; ?>"
                                data-contact-number="<?= $household['contact_number']; ?>">
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- Archive Button -->
                            <button 
                                class="btn btn-danger" 
                                data-id="<?= $household['household_id'] ?>" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#archiveModal">
                                <i class="fas fa-archive"></i>
                            </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="11" class="text-center">No household heads found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="household.php?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <!-- Add Household Head Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="add_household.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Add Household Head</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Add Household Fields -->
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="middleName" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="middle_name">
                        </div>
                        <div class="mb-3">
                            <label for="birthdate" class="form-label">Birthdate</label>
                            <input type="date" class="form-control" name="birthdate" required oninput="calculateAge()">
                        </div>
                        <div class="mb-3">
                            <label for="civilStatus" class="form-label">Civil Status</label>
                            <select class="form-control" name="civil_status">
                                <option>-select-</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Live In">Live In</option>
                                <option value="Separated">Separated</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-control" name="gender">
                                <option>-select-</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tribe" class="form-label">Tribe</label>
                            <select class="form-control" id="tribe" name="tribe" required>
                                <option>-select-</option>
                                <option value="none">-None-</option>
                                <option value="Ata Manobo">Ata Manobo</option>
                                <option value="Badjao">Badjao</option>
                                <option value="Bagobo">Bagobo</option>
                                <option value="Banwaon">Banwaon</option>
                                <option value="B laan">B laan</option>
                                <option value="Bukidnon">Bukidnon</option>
                                <option value="Dibabanwa">Dibabanwa</option>
                                <option value="Dulangan">Dulangan</option>
                                <option value="Guiangga">Guiangga</option>
                                <option value="Higaonon">Higaonon</option>
                                <option value="JamaMapun">JamaMapun</option>
                                <option value="Kaagan">Kaagan</option>
                                <option value="Kalagan">Kalagan</option>
                                <option value="Kulangan">Kulangan</option>
                                <option value="Kalbugan">Kalbugan</option>
                                <option value="Magindanaon">Magindanaon</option>
                                <option value="Magguangan">Magguangan</option>
                                <option value="Mamanwa">Mamanwa</option>
                                <option value="Mandaya">Mandaya</option>
                                <option value="Mangguwangan">Mangguwangan</option>
                                <option value="Manobo">Manobo</option>
                                <option value="Malbog">Malbog</option>
                                <option value="Maramo">Maramo</option>
                                <option value="Mansaka">Mansaka</option>
                                <option value="Matigsalog">Matigsalog</option>
                                <option value="Palawani">Palawani</option>
                                <option value="Sama">Sama</option>
                                <option value="Sangil">Sangil</option>
                                <option value="Subanon">Subanon</option>
                                <option value="Tagakaolo">Tagakaolo</option>
                                <option value="T boli">T boli</option>
                                <option value="Talandig">Talandig</option>
                                <option value="Tao-sug">Tao-sug</option>
                                <option value="Teduary">Teduary</option>
                                <option value="Ubo">Ubo</option>
                                <option value="Yakan">Yakan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="occupation" class="form-label">Occupation</label>
                            <select class="form-control" id="occupation" name="occupation" required>
                                <option>-select-</option>
                                <option value="None">None</option>
                                <option value="Accountant">Accountant</option>
                                <option value="Assistant">Assistant</option>
                                <option value="Baker">Baker</option>
                                <option value="Barber">Barber</option>
                                <option value="Bookkeeper">Bookkeeper</option>
                                <option value="Businessman/woman">Businessman/woman</option>
                                <option value="Butcher">Butcher</option>
                                <option value="Carpenter">Carpenter</option>
                                <option value="Cahsier">Cashier</option>
                                <option value="Construction Worker">Construction Worker</option>
                                <option value="Civil Servant">Civil Servant</option>
                                <option value="Chef">Chef</option>
                                <option value="Doctor">Doctor</option>
                                <option value="Dentist">Dentist</option>
                                <option value="Driver">Driver</option>
                                <option value="Electrician">Electrician</option>
                                <option value="Farmer">Farmer</option>
                                <option value="Firefighter">Firefighter</option>
                                <option value="Fisherman">Fisherman</option>
                                <option value="Housekeeper">Housekeeper</option>
                                <option value="Housewife">Housewife</option>
                                <option value="Lawyer">Lawyer</option>
                                <option value="Manager">Manager</option>
                                <option value="Nurse">Nurse</option>
                                <option value="Office Cleark">Office Clerk</option>
                                <option value="Overseas Filipino Worker (OFW)">Overseas Filipino Worker (OFW)</option>
                                <option value="Police Officer">Police Officer</option>
                                <option value="Salesperson">Salesperson</option>
                                <option value="Seaman/woman">Seaman/woman</option>
                                <option value="Soldier">Soldier</option>
                                <option value="Teacher">Teacher</option>
                                <option value="Vendor">Vendor</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <select class="form-control" id="address" name="address" required>
                                <option>-select-</option>
                                <option value="Purok Sto.Niño-Apo Beach">Purok Sto.Niño-Apo Beach</option>
                                <option value="Purok Bayabas-Apo Beach">Purok Bayabas-Apo Beach</option>
                                <option value="Purok Centro-Apo Beach">Purok Centro-Apo Beach</option>
                                <option value="Purok Leytenians-Apo Beach">Purok Leytenians-Apo Beach</option>
                                <option value="Purok Mahayahay-Apo Beach">Purok Mahayahay-Apo Beach</option>
                                <option value="Purok Kalubihan-Apo Beach">Purok Kalubihan-Apo Beach</option>
                                <option value="Purok Badjaoan-Apo Beach">Purok Badjaoan-Apo Beach</option>
                                <option value="Purok Bonggahan">Purok Bonggahan</option>
                                <option value="Purok Madasigon">Purok Madasigon</option>
                                <option value="Purok Kapayapaan-Amlo Subd">Purok Kapayapaan-Amlo Subd</option>
                                <option value="Purok Bougainvilla">Purok Bougainvilla</option>
                                <option value="Purok Federation President">Purok Federation President</option>
                                <option value="Purok Miranda">Purok Miranda</option>
                                <option value="Purok Kaunlaran-Sto.Niño Village">Purok Kaunlaran-Sto.Niño Village</option>
                                <option value="Purok Talisay-Ceboley Beach">Purok Talisay-Ceboley Beach</option>
                                <option value="Purok Dapsap-Ceboley Beach">Purok Dapsap-Ceboley Beach</option>
                                <option value="Purok Sampaguita-Ceboley Beach">Purok Sampaguita-Ceboley Beach</option>
                                <option value="Purok Kagitingan-Kapihan">Purok Kagitingan-Kapihan</option>
                                <option value="Purok Kaimito-Bagumbayan">Purok Kaimito-Bagumbayan</option>
                                <option value="Purok Pakigdait-Bagumbayan">Purok Pakigdait-Bagumbayan</option>
                                <option value="Purok Pagkakaisa-Bagumbayan">Purok Pagkakaisa-Bagumbayan</option>
                                <option value="Purok Pag-asa-Bagumbayan">Purok Pag-asa-Bagumbayan</option>
                                <option value="Purok Bagong Silang-sitio Doring Bendigo">Purok Bagong Silang-sitio Doring Bendigo</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="contactNumber" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" name="contact_number">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Household Head</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="edit_household.php" method="POST">
                    <input type="hidden" name="household_id" id="editHouseholdId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Household Head</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Same fields as in the Add Modal, but pre-populated -->
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" id="editLastName" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" id="editFirstName" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="middleName" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="middle_name" id="editMiddleName" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="birthdate" class="form-label">Birthdate</label>
                            <input type="date" class="form-control" name="birthdate" required oninput="calculateAge()" autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="civilStatus" class="form-label">Civil Status</label>
                            <select class="form-control" name="civil_status" id="editcivil_status" autofocus>
                                <option>-select-</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Live In">Live In</option>
                                <option value="Separated">Separated</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-control" name="gender" id="editGender" autofocus>
                                <option>-select-</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tribe" class="form-label">Tribe</label>
                            <select class="form-control" id="editTribe" name="tribe" required autofocus>
                                <option>-select-</option>
                                <option value="none">-None-</option>
                                <option value="Ata Manobo">Ata Manobo</option>
                                <option value="Badjao">Badjao</option>
                                <option value="Bagobo">Bagobo</option>
                                <option value="Banwaon">Banwaon</option>
                                <option value="B laan">B laan</option>
                                <option value="Bukidnon">Bukidnon</option>
                                <option value="Dibabanwa">Dibabanwa</option>
                                <option value="Dulangan">Dulangan</option>
                                <option value="Guiangga">Guiangga</option>
                                <option value="Higaonon">Higaonon</option>
                                <option value="JamaMapun">JamaMapun</option>
                                <option value="Kaagan">Kaagan</option>
                                <option value="Kalagan">Kalagan</option>
                                <option value="Kulangan">Kulangan</option>
                                <option value="Kalbugan">Kalbugan</option>
                                <option value="Magindanaon">Magindanaon</option>
                                <option value="Magguangan">Magguangan</option>
                                <option value="Mamanwa">Mamanwa</option>
                                <option value="Mandaya">Mandaya</option>
                                <option value="Mangguwangan">Mangguwangan</option>
                                <option value="Manobo">Manobo</option>
                                <option value="Malbog">Malbog</option>
                                <option value="Maramo">Maramo</option>
                                <option value="Mansaka">Mansaka</option>
                                <option value="Matigsalog">Matigsalog</option>
                                <option value="Palawani">Palawani</option>
                                <option value="Sama">Sama</option>
                                <option value="Sangil">Sangil</option>
                                <option value="Subanon">Subanon</option>
                                <option value="Tagakaolo">Tagakaolo</option>
                                <option value="T boli">T boli</option>
                                <option value="Talandig">Talandig</option>
                                <option value="Tao-sug">Tao-sug</option>
                                <option value="Teduary">Teduary</option>
                                <option value="Ubo">Ubo</option>
                                <option value="Yakan">Yakan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="occupation" class="form-label">Occupation</label>
                            <select class="form-control" id="editOccupation" name="occupation" required autofocus>
                                <option>-select-</option>
                                <option value="None">None</option>
                                <option value="Accountant">Accountant</option>
                                <option value="Assistant">Assistant</option>
                                <option value="Baker">Baker</option>
                                <option value="Barber">Barber</option>
                                <option value="Bookkeeper">Bookkeeper</option>
                                <option value="Businessman/woman">Businessman/woman</option>
                                <option value="Butcher">Butcher</option>
                                <option value="Carpenter">Carpenter</option>
                                <option value="Cahsier">Cashier</option>
                                <option value="Construction Worker">Construction Worker</option>
                                <option value="Civil Servant">Civil Servant</option>
                                <option value="Chef">Chef</option>
                                <option value="Doctor">Doctor</option>
                                <option value="Dentist">Dentist</option>
                                <option value="Driver">Driver</option>
                                <option value="Electrician">Electrician</option>
                                <option value="Farmer">Farmer</option>
                                <option value="Firefighter">Firefighter</option>
                                <option value="Fisherman">Fisherman</option>
                                <option value="Housekeeper">Housekeeper</option>
                                <option value="Housewife">Housewife</option>
                                <option value="Lawyer">Lawyer</option>
                                <option value="Manager">Manager</option>
                                <option value="Nurse">Nurse</option>
                                <option value="Office Cleark">Office Clerk</option>
                                <option value="Overseas Filipino Worker (OFW)">Overseas Filipino Worker (OFW)</option>
                                <option value="Police Officer">Police Officer</option>
                                <option value="Salesperson">Salesperson</option>
                                <option value="Seaman/woman">Seaman/woman</option>
                                <option value="Soldier">Soldier</option>
                                <option value="Teacher">Teacher</option>
                                <option value="Vendor">Vendor</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <select class="form-control" id="editAddress" name="address" required autofocus>
                                <option>-select-</option>
                                <option value="Purok Sto.Niño-Apo Beach">Purok Sto.Niño-Apo Beach</option>
                                <option value="Purok Bayabas-Apo Beach">Purok Bayabas-Apo Beach</option>
                                <option value="Purok Centro-Apo Beach">Purok Centro-Apo Beach</option>
                                <option value="Purok Leytenians-Apo Beach">Purok Leytenians-Apo Beach</option>
                                <option value="Purok Mahayahay-Apo Beach">Purok Mahayahay-Apo Beach</option>
                                <option value="Purok Kalubihan-Apo Beach">Purok Kalubihan-Apo Beach</option>
                                <option value="Purok Badjaoan-Apo Beach">Purok Badjaoan-Apo Beach</option>
                                <option value="Purok Bonggahan">Purok Bonggahan</option>
                                <option value="Purok Madasigon">Purok Madasigon</option>
                                <option value="Purok kapayapaan-Amlo Subd">Purok kapayapaan-Amlo Subd</option>
                                <option value="Purok Bougainvilla">Purok Bougainvilla</option>
                                <option value="Purok Federation President">Purok Federation President</option>
                                <option value="Purok Miranda">Purok Miranda</option>
                                <option value="Purok Kaunlaran-Sto.Niño Village">Purok Kaunlaran-Sto.Niño Village</option>
                                <option value="Purok Talisay-Ceboley Beach">Purok Talisay-Ceboley Beach</option>
                                <option value="Purok Dapsap-Ceboley Beach">Purok Dapsap-Ceboley Beach</option>
                                <option value="Purok Sampaguita-Ceboley Beach">Purok Sampaguita-Ceboley Beach</option>
                                <option value="Purok Kagitingan-Kapihan">Purok Kagitingan-Kapihan</option>
                                <option value="Purok Kaimito-Bagumbayan">Purok Kaimito-Bagumbayan</option>
                                <option value="Purok Pakigdait-Bagumbayan">Purok Pakigdait-Bagumbayan</option>
                                <option value="Purok Pagkakaisa-Bagumbayan">Purok Pagkakaisa-Bagumbayan</option>
                                <option value="Purok Pag-asa-Bagumbayan">Purok Pag-asa-Bagumbayan</option>
                                <option value="Purok Bagong Silang-sitio Doring Bendigo">Purok Bagong Silang-sitio Doring Bendigo</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="contactNumber" class="form-label">Contact Number</label autofocus>
                            <input type="text" class="form-control" name="contact_number" id="editContactNumber" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Archive Modal -->
    <div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="archive_household.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="archiveModalLabel">Archive Household</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to archive this household?
                        <input type="hidden" id="archive-household-id" name="household_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Archive</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        //Edit
        document.addEventListener('DOMContentLoaded', function () {
            const editModal = document.getElementById('editModal');
            editModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget; // Button that triggered the modal
                const householdId = button.getAttribute('data-id'); // Get the household_id

                // Populate the hidden input and other fields via AJAX or directly from the button's data attributes
                document.getElementById('editHouseholdId').value = householdId;
                document.getElementById('editLastName').value = button.getAttribute('data-last-name') || '';
                document.getElementById('editFirstName').value = button.getAttribute('data-first-name') || '';
                document.getElementById('editMiddleName').value = button.getAttribute('data-middle-name') || '';
                document.getElementById('editGender').value = button.getAttribute('data-gender') || '';
                document.getElementById('editcivil_status').value = button.getAttribute('data-civil-status') || '';
                document.getElementById('editTribe').value = button.getAttribute('data-tribe') || '';
                document.getElementById('editOccupation').value = button.getAttribute('data-occupation') || '';
                document.getElementById('editAddress').value = button.getAttribute('data-address') || '';
                document.getElementById('editContactNumber').value = button.getAttribute('data-contact-number') || '';
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Archive Modal Event Listener
            const archiveModal = document.getElementById('archiveModal');
            archiveModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const householdId = button.getAttribute('data-id');
                document.getElementById('archive-household-id').value = householdId;
            });
        });



        //Alert
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.classList.add('fade-out');
            }
        }, 5000); // Adjust the timeout (5 seconds)


        // Age Calculation
        function calculateAge() {
                    const birthdate = new Date(document.getElementById('birthdate').value);
                    const today = new Date();
                    let age = today.getFullYear() - birthdate.getFullYear();
                    const monthDifference = today.getMonth() - birthdate.getMonth();

                    if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthdate.getDate())) {
                        age--;
                    }
                    document.getElementById('age').value = age;
            }
    </script>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>