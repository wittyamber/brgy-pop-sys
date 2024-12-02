<?php
    include 'config.php'; // Database connection
    include 'side_nav.php'; // Navigation

    // Pagination settings
    $results_per_page = 5; // Number of records per page
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start_from = ($current_page - 1) * $results_per_page;

    // Search functionality
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // Fetch archived households with search and pagination
    $sql = "SELECT * FROM households WHERE archived = 1 AND (last_name LIKE ? OR first_name LIKE ?) LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $search_term = "%$search%";
    $stmt->bind_param("ssii", $search_term, $search_term, $start_from, $results_per_page);
    $stmt->execute();
    $result = $stmt->get_result();

    // Count total records for pagination
    $count_sql = "SELECT COUNT(*) AS total FROM households WHERE archived = 1 AND (last_name LIKE ? OR first_name LIKE ?)";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("ss", $search_term, $search_term);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_records = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($total_records / $results_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBPMMS | Archived Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">

        <h3>Archived Household Heads</h3>

        <!-- Search Bar -->
        <form method="GET" action="" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by name..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </div>
        </form>

        <!-- Archived Records Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Address</th>
                    <th>Contact Number</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['household_id']}</td>
                            <td>{$row['last_name']}, {$row['first_name']} {$row['middle_name']}</td>
                            <td>{$row['address']}</td>
                            <td>{$row['contact_number']}</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No archived records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                    <li class="page-item <?php echo $page == $current_page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page; ?>&search=<?php echo urlencode($search); ?>"><?php echo $page; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>

        <h3 class="mt-5">Archived Household Members</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Member ID</th>
                    <th>Household ID</th>
                    <th>Name</th>
                    <th>Relationship</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM household_members WHERE status = 'Archived'"; // Fetch archived members
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['member_id']}</td>
                            <td>{$row['household_id']}</td>
                            <td>{$row['last_name']}, {$row['first_name']}</td>
                            <td>{$row['relationship_to_head']}</td>
                            <td>{$row['status']}</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No archived household members found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                    <li class="page-item <?php echo $page == $current_page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page; ?>&search=<?php echo urlencode($search); ?>"><?php echo $page; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
