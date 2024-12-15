<?php
    include 'config.php'; // Database connection
    include 'side_nav.php'; // Navigation

    // Households
    $results_per_page = 10; 
    $current_page = isset($_GET['page_households']) ? (int)$_GET['page_households'] : 1;
    $start_from = ($current_page - 1) * $results_per_page;

    $search = isset($_GET['search_households']) ? $_GET['search_households'] : '';
    $search_term = "%$search%";

    $sql_households = "
        SELECT 
            h.household_id,
            h.household_number,
            h.contact_number,
            h.total_members,
            hh.last_name,
            hh.first_name,
            h.purok_id
        FROM household h
        JOIN household_members hh ON h.household_head_id = hh.member_id
        WHERE h.archived = 1 
        AND (hh.last_name LIKE ? OR hh.first_name LIKE ? OR h.household_number LIKE ?)
        LIMIT ? OFFSET ?";
    $stmt_households = $conn->prepare($sql_households);
    $stmt_households->bind_param("ssiii", $search_term, $search_term, $search_term, $results_per_page, $start_from);
    $stmt_households->execute();
    $result_households = $stmt_households->get_result();

    $count_sql_households = "
        SELECT COUNT(*) AS total
        FROM household h
        JOIN household_members hh ON h.household_head_id = hh.member_id
        WHERE h.archived = 1 
        AND (hh.last_name LIKE ? OR hh.first_name LIKE ? OR h.household_number LIKE ?)";
    $count_stmt_households = $conn->prepare($count_sql_households);
    $count_stmt_households->bind_param("sss", $search_term, $search_term, $search_term);
    $count_stmt_households->execute();
    $total_households = $count_stmt_households->get_result()->fetch_assoc()['total'];
    $total_pages_households = ceil($total_households / $results_per_page);

    // Residents
    $results_per_page_members = 10; 
    $current_page_members = isset($_GET['page_members']) ? (int)$_GET['page_members'] : 1;
    $start_from_members = ($current_page_members - 1) * $results_per_page_members;

    $search_members = isset($_GET['search_members']) ? $_GET['search_members'] : '';
    $search_members_term = "%$search_members%";

    $sql_members = "
        SELECT 
            m.member_id,
            m.household_id,
            m.last_name,
            m.first_name,
            m.relationship_to_head
        FROM household_members m
        WHERE m.archived = 1 
        AND (m.last_name LIKE ? OR m.first_name LIKE ? OR m.relationship_to_head LIKE ?)
        LIMIT ?, ?";
    $stmt_members = $conn->prepare($sql_members);
    $stmt_members->bind_param("sssii", $search_members_term, $search_members_term, $search_members_term, $start_from_members, $results_per_page_members);
    $stmt_members->execute();
    $result_members = $stmt_members->get_result();

    $count_sql_members = "
        SELECT COUNT(*) AS total
        FROM household_members m
        WHERE m.archived = 1 
        AND (m.last_name LIKE ? OR m.first_name LIKE ? OR m.relationship_to_head LIKE ?)";
    $count_stmt_members = $conn->prepare($count_sql_members);
    $count_stmt_members->bind_param("sss", $search_members_term, $search_members_term, $search_members_term);
    $count_stmt_members->execute();
    $total_members = $count_stmt_members->get_result()->fetch_assoc()['total'];
    $total_pages_members = ceil($total_members / $results_per_page_members);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBPMMS | Archived Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="container mt-5">

        <!-- Archived Household Heads -->
        <h3>Archived Households</h3>
        <form method="GET" action="" class="mb-3">
            <div class="input-group">
                <input type="text" name="search_households" class="form-control" placeholder="Search by name or household number..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </div>
        </form>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Household Number</th>
                    <th>Head Name</th>
                    <th>Purok ID</th>
                    <th>Total Members</th>
                    <th>Contact Number</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_households->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['household_id'] ?></td>
                        <td><?= $row['household_number'] ?></td>
                        <td><?= $row['last_name'] ?>, <?= $row['first_name'] ?></td>
                        <td><?= $row['purok_id'] ?></td>
                        <td><?= $row['total_members'] ?></td>
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
