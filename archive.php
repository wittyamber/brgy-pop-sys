<?php
    include 'config.php'; // Database connection
    include 'side_nav.php'; // Navigation

    // Pagination for household heads
    $results_per_page = 10; 
    $current_page = isset($_GET['page_households']) ? (int)$_GET['page_households'] : 1;
    $start_from = ($current_page - 1) * $results_per_page;

    // Search for household heads
    $search = isset($_GET['search_households']) ? $_GET['search_households'] : '';
    $search_term = "%$search%";

    // Fetch archived households
    $sql_households = "SELECT * FROM households WHERE archived = 1 AND (last_name LIKE ? OR first_name LIKE ?) LIMIT ?, ?";
    $stmt_households = $conn->prepare($sql_households);
    $stmt_households->bind_param("ssii", $search_term, $search_term, $start_from, $results_per_page);
    $stmt_households->execute();
    $result_households = $stmt_households->get_result();

    // Total records for household pagination
    $count_sql_households = "SELECT COUNT(*) AS total FROM households WHERE archived = 1 AND (last_name LIKE ? OR first_name LIKE ?)";
    $count_stmt_households = $conn->prepare($count_sql_households);
    $count_stmt_households->bind_param("ss", $search_term, $search_term);
    $count_stmt_households->execute();
    $total_households = $count_stmt_households->get_result()->fetch_assoc()['total'];
    $total_pages_households = ceil($total_households / $results_per_page);

    // Pagination for household members
    $current_page_members = isset($_GET['page_members']) ? (int)$_GET['page_members'] : 1;
    $start_from_members = ($current_page_members - 1) * $results_per_page;

    // Search for household members
    $search_members = isset($_GET['search_members']) ? $_GET['search_members'] : '';
    $search_term_members = "%$search_members%";

    // Fetch archived household members
    $sql_members = "SELECT * FROM household_members WHERE archived = 1 AND (first_name LIKE ? OR last_name LIKE ? OR relationship_to_head LIKE ?) LIMIT ?, ?";
    $stmt_members = $conn->prepare($sql_members);
    $stmt_members->bind_param("sssii", $search_term_members, $search_term_members, $search_term_members, $start_from_members, $results_per_page);
    $stmt_members->execute();
    $result_members = $stmt_members->get_result();

    // Total records for members pagination
    $count_sql_members = "SELECT COUNT(*) AS total FROM household_members WHERE archived = 1 AND (first_name LIKE ? OR last_name LIKE ? OR relationship_to_head LIKE ?)";
    $count_stmt_members = $conn->prepare($count_sql_members);
    $count_stmt_members->bind_param("sss", $search_term_members, $search_term_members, $search_term_members);
    $count_stmt_members->execute();
    $total_members = $count_stmt_members->get_result()->fetch_assoc()['total'];
    $total_pages_members = ceil($total_members / $results_per_page);
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

        <!-- Archived Household Heads -->
        <h3>Archived Household Heads</h3>
        <form method="GET" action="" class="mb-3">
            <div class="input-group">
                <input type="text" name="search_households" class="form-control" placeholder="Search by name..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </div>
        </form>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Address</th>
                    <th>Contact Number</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_households->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['household_id'] ?></td>
                        <td><?= $row['last_name'] ?>, <?= $row['first_name'] ?></td>
                        <td><?= $row['address'] ?></td>
                        <td><?= $row['contact_number'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($page = 1; $page <= $total_pages_households; $page++): ?>
                    <li class="page-item <?= $page == $current_page ? 'active' : '' ?>">
                        <a class="page-link" href="?page_households=<?= $page ?>&search_households=<?= urlencode($search) ?>"><?= $page ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>

        <!-- Archived Residents -->
        <h3 class="mt-5">Archived Residents</h3>
        <form method="GET" action="" class="mb-3">
            <div class="input-group">
                <input type="text" name="search_members" class="form-control" placeholder="Search by name or relationship..." value="<?= htmlspecialchars($search_members) ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </div>
        </form>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Household ID</th>
                    <th>Name</th>
                    <th>Relationship</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_members->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['member_id'] ?></td>
                        <td><?= $row['household_id'] ?></td>
                        <td><?= $row['last_name'] ?>, <?= $row['first_name'] ?></td>
                        <td><?= $row['relationship_to_head'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($page = 1; $page <= $total_pages_members; $page++): ?>
                    <li class="page-item <?= $page == $current_page_members ? 'active' : '' ?>">
                        <a class="page-link" href="?page_members=<?= $page ?>&search_members=<?= urlencode($search_members) ?>"><?= $page ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
