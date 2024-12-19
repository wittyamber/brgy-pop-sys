<?php
    include '../config.php';
    include 'side_navigation.php';
    include '../includes/alerts.php';

    // Fetch Data for Display
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $filter_purok = isset($_GET['filter_purok']) ? $_GET['filter_purok'] : '';
    $limit = 5; // Number of households per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Filtering and Search Query
    $where = "WHERE h.archived = 0";
    if (!empty($search)) {
        $where .= " AND (h.household_number LIKE '%$search%' 
                    OR CONCAT(m.first_name, ' ', m.last_name) LIKE '%$search%' 
                    OR p.purok_name LIKE '%$search%')";
    }
    if (!empty($filter_purok)) {
        $where .= " AND p.purok_id = '$filter_purok'";
    }

    // Count total households for pagination
    $total_result = $conn->query("SELECT COUNT(*) AS total FROM household h 
                                JOIN puroks p ON h.purok_id = p.purok_id 
                                JOIN household_members m ON h.household_head_id = m.member_id $where");
    $total_households = $total_result->fetch_assoc()['total'];
    $total_pages = ceil($total_households / $limit);

    // Fetch paginated results
    $sql = "SELECT h.household_id, h.household_number, p.purok_name, 
                CONCAT(m.first_name, ' ', m.last_name) AS household_head, 
                h.contact_number, h.total_members 
            FROM household h
            JOIN puroks p ON h.purok_id = p.purok_id
            JOIN household_members m ON h.household_head_id = m.member_id
            $where
            ORDER BY h.household_number ASC
            LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);

    // Fetch all puroks for filtering
    $puroks = $conn->query("SELECT * FROM puroks ORDER BY purok_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBPMMS | Household</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="../css/household.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Households</h2>

        <!-- Search, Filter, and Add Household -->
        <form class="row my-4 g-1 align-items-center" method="get">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search households..." value="<?= $search; ?>">
            </div>

            <div class="col-md-3">
                <select name="filter_purok" class="form-select">
                    <option value="">All Puroks</option>
                    <?php while ($purok = $puroks->fetch_assoc()) : ?>
                        <option value="<?= $purok['purok_id']; ?>" <?= $filter_purok == $purok['purok_id'] ? 'selected' : ''; ?>>
                            <?= $purok['purok_name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Search</button>
            </div>

            <div class="col-md-2">
                <a href="cap_household.php" class="btn btn-secondary w-100">
                    <i class="fas fa-undo"></i> Reset Filter
                </a>
            </div>

            <div class="col-md-2">
                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#addHouseholdModal">
                    <i class="fas fa-plus"></i> Add Household
                </button>
            </div>
        </form>


        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 2%;">No.</th> <!-- Add a column for number -->
                        <th style="width: 5%;">Household Number</th>
                        <th>Purok</th>
                        <th>Household Head</th>
                        <th>Total Members</th>
                        <th>Contact Number</th>
                        <th>Tools</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Initialize the counter variable
                    $counter = 1;
                    if ($result->num_rows > 0) :
                        while ($row = $result->fetch_assoc()) :
                    ?>
                            <tr>
                                <!-- Display the counter number -->
                                <td><?= $counter++; ?></td> <!-- Increment the counter after displaying it -->
                                <td><?= $row['household_number']; ?></td>
                                <td><?= $row['purok_name']; ?></td>
                                <td><?= $row['household_head']; ?></td>
                                <td><?= $row['total_members']; ?></td>
                                <td><?= $row['contact_number']; ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="viewMembers(<?= $row['household_id']; ?>)"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-warning btn-sm" onclick="openEditModal(
                                        '<?= $row['household_id']; ?>',
                                        '<?= $row['household_number']; ?>',
                                        '<?= $row['purok_name']; ?>',
                                        '<?= $row['household_head']; ?>',
                                        '<?= $row['contact_number']; ?>',
                                        '<?= $row['total_members']; ?>'
                                    )"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm" onclick="openArchiveModal(<?= $row['household_id']; ?>)"><i class="fas fa-archive"></i></button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center">No households found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?= $i; ?>&search=<?= $search; ?>&filter_purok=<?= $filter_purok; ?>">
                            <?= $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <!-- Add Household Modal -->
    <div class="modal fade" id="addHouseholdModal" tabindex="-1" aria-labelledby="addHouseholdModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="cap_add_household.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addHouseholdModalLabel">Add Household</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="household_number" class="form-label">Household Number</label>
                            <input type="text" class="form-control" name="household_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="purok_id" class="form-label">Purok</label>
                            <select name="purok_id" class="form-select" required>
                                <option value="">Select Purok</option>
                                <?php foreach ($puroks as $purok): ?>
                                    <option value="<?= $purok['purok_id']; ?>"><?= $purok['purok_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="household_head" class="form-label">Household Head</label>
                            <select name="household_head" class="form-select" required>
                                <option value="">Select Household Head</option>
                                <?php
                                    $active_members = $conn->query("SELECT member_id, CONCAT(first_name, ' ', last_name) AS name FROM household_members WHERE archived = 0");
                                    while ($member = $active_members->fetch_assoc()) :
                                ?>
                                    <option value="<?= $member['member_id']; ?>"><?= $member['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="total_members" class="form-label">Total Members</label>
                            <input type="number" name="total_members" class="form-control" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" name="contact_number">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" name="add_household">Add Household</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Members Modal -->
    <div class="modal fade" id="viewMembersModal" tabindex="-1" aria-labelledby="viewMembersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewMembersModalLabel">Household Members</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Member Name</th>
                                <th>Relationship</th>
                            </tr>
                        </thead>
                        <tbody id="householdMembersTable"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Household Modal -->
    <div class="modal fade" id="editHouseholdModal" tabindex="-1" aria-labelledby="editHouseholdModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <form id="editHouseholdForm" method="post" action="cap_edit_household.php">
                <div class="modal-header">
                <h5 class="modal-title" id="editHouseholdModalLabel">Edit Household</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <input type="hidden" id="editHouseholdId" name="household_id">

                <div class="mb-3">
                    <label for="editHouseholdNumber" class="form-label">Household Number</label>
                    <input type="text" class="form-control" id="editHouseholdNumber" name="household_number" required>
                </div>

                <div class="mb-3">
                    <label for="editPurok" class="form-label">Purok</label>
                    <select class="form-select" id="editPurok" name="purok_id" required>
                        <option value="">-- Select Purok --</option>
                            <?php
                                $purok_result = $conn->query("SELECT * FROM puroks");
                                while ($purok = $purok_result->fetch_assoc()) :
                            ?>
                                <option value="<?= $purok['purok_id']; ?>"><?= $purok['purok_name']; ?></option>
                            <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="editHouseholdHead" class="form-label">Household Head</label>
                    <select class="form-select" id="editHouseholdHead" name="household_head_id" required>
                        <option value="">-- Select Household Head --</option>
                            <?php
                                $head_result = $conn->query("SELECT * FROM household_members WHERE archived = 0");
                                while ($head = $head_result->fetch_assoc()) :
                            ?>
                                <option value="<?= $head['member_id']; ?>"><?= $head['first_name'] . ' ' . $head['last_name']; ?></option>
                            <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="editContactNumber" class="form-label">Contact Number</label>
                    <input type="text" class="form-control" id="editContactNumber" name="contact_number" required>
                </div>

                <div class="mb-3">
                    <label for="editTotalMembers" class="form-label">Total Members</label>
                    <input type="number" class="form-control" id="editTotalMembers" name="total_members" required>
                </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="update_household" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <!-- Archive Household Modal -->
    <div class="modal fade" id="archiveHouseholdModal" tabindex="-1" aria-labelledby="archiveHouseholdModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <form id="archiveHouseholdForm" method="post" action="cap_archive_household.php">
                <div class="modal-header">
                <h5 class="modal-title" id="archiveHouseholdModalLabel">Archive Household</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <input type="hidden" id="archiveHouseholdId" name="household_id">
                <p>Are you sure you want to archive this household?</p>
                <p class="text-muted">This action cannot be undone, but the household can be reactivated by an administrator if needed.</p>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="archive_household" class="btn btn-danger">Archive</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        //View Members Modal
        function viewMembers(householdId) {
            $.post("cap_fetch_household_members.php", { household_id: householdId }, function(data) {
                $("#householdMembersTable").html(data);
                $("#viewMembersModal").modal("show");
            });
        }

        //Edit Modal
        function openEditModal(householdId, householdNumber, purokId, householdHeadId, contactNumber, totalMembers) {
        document.getElementById('editHouseholdId').value = householdId;
        document.getElementById('editHouseholdNumber').value = householdNumber;
        document.getElementById('editPurok').value = purokId;
        document.getElementById('editHouseholdHead').value = householdHeadId;
        document.getElementById('editContactNumber').value = contactNumber;
        document.getElementById('editTotalMembers').value = totalMembers;
        $('#editHouseholdModal').modal('show');
        }

        // Populate Archive Modal
        function openArchiveModal(householdId) {
        document.getElementById('archiveHouseholdId').value = householdId;
        $('#archiveHouseholdModal').modal('show');
        }

    </script>
</body>
</html>
