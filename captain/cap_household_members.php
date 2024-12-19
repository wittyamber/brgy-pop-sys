<?php
    require '../config.php'; 
    include 'side_navigation.php';
    include '../includes/alerts.php';

    // Pagination variables
    $limit = 10; 
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($page - 1) * $limit;

    // Get filter values from the GET request
    $search = $_GET['search'] ?? ''; 
    $filterPurok = $_GET['purok'] ?? '';
    $filterPosition = $_GET['position'] ?? '';
    $filterStatus = $_GET['status'] ?? '';

    $conditions = "WHERE bo.archived = 0 ";  // Start with basic condition (non-archived officials)
    $params = [];
    $types = "";

    // Search filter
    if (!empty($search)) {
        $conditions .= "AND (bo.name LIKE ? OR bo.position LIKE ? OR bo.purok_name LIKE ?) ";
        $searchWildcard = "%$search%";
        $params[] = $searchWildcard;
        $params[] = $searchWildcard;
        $params[] = $searchWildcard;
        $types .= "sss";
    }

    // Purok filter
    if (!empty($filterPurok)) {
        $conditions .= "AND bo.purok_name = ? ";
        $params[] = $filterPurok;
        $types .= "s";
    }

    // Position filter
    if (!empty($filterPosition)) {
        $conditions .= "AND bo.position = ? ";
        $params[] = $filterPosition;
        $types .= "s";
    }

    // Status filter
    if (!empty($filterStatus)) {
        $conditions .= "AND bo.status = ? ";
        $params[] = $filterStatus;
        $types .= "s";
    }

    // Add pagination and ordering
    $conditions .= "ORDER BY bo.name ASC LIMIT ?, ?";
    $params[] = $start;
    $params[] = $limit;
    $types .= "ii";

    // Prepare the query for fetching barangay officials
    $query = "
        SELECT bo.*, p.purok_name 
        FROM barangay_officials bo
        JOIN puroks p ON bo.purok_id = p.purok_id
        $conditions
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $officials = $result->fetch_all(MYSQLI_ASSOC);

    // Count the total number of records for pagination
    $countQuery = "
        SELECT COUNT(*) AS total
        FROM barangay_officials bo
        JOIN puroks p ON bo.purok_id = p.purok_id
        WHERE bo.archived = 0
    ";
    $countResult = $conn->query($countQuery);
    $totalRecords = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRecords / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBPMMS | Residents</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="../css/household.css">
</head>
<body>
    <div class="container mt-5">

        <h2 class="text-center">Residents</h2>

        <!-- Search Bar and Filters -->
        <form method="GET" action="cap_household_members.php" class="mb-3">
            <div class="row g-2"> 
                <div class="col-md-3">
                    <input
                        class="form-control"
                        type="text"
                        name="search"
                        placeholder="Search by name, address, etc."
                        value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="purok">
                        <option value="">Purok</option>
                        <?php
                            $purokResult = $conn->query("SELECT DISTINCT purok_name FROM puroks ORDER BY purok_name ASC");
                            while ($purok = $purokResult->fetch_assoc()):
                        ?>
                            <option value="<?= $purok['purok_name'] ?>" <?= $purok['purok_name'] == $filterPurok ? 'selected' : '' ?>>
                                <?= $purok['purok_name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="gender">
                        <option value="">Gender</option>
                        <option value="Male" <?= $filterGender == 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= $filterGender == 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="civil_status">
                        <option value="">Civil Status</option>
                        <option value="Single" <?= $filterCivilStatus == 'Single' ? 'selected' : '' ?>>Single</option>
                        <option value="Married" <?= $filterCivilStatus == 'Married' ? 'selected' : '' ?>>Married</option>
                        <option value="Widowed" <?= $filterCivilStatus == 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                        <option value="Live In" <?= $filterCivilStatus == 'Live In' ? 'selected' : '' ?>>Live In</option>
                        <option value="Separated" <?= $filterCivilStatus == 'Separated' ? 'selected' : '' ?>>Separated</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary w-50 me-2">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="cap_household_members.php" class="btn btn-secondary w-50">
                        <i class="fas fa-undo"></i> Reset Filters
                    </a>
                </div>
            </div>
        </form>

        <!-- Add Resident Button -->
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                <i class="fas fa-plus"></i> Add Resident
            </button>
        </div>

        <!-- Members Table -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Full Name</th>
                    <th>Household Number</th>
                    <th>Birthdate</th>
                    <th>Age</th>
                    <th>Civil Status</th>
                    <th>Gender</th>
                    <th>Relationship to Head</th>
                    <th>Tribe</th>
                    <th>Occupation</th>
                    <th>Purok</th>
                    <th style="width: 125px">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($members)): ?>
                    <?php foreach ($members as $index => $member): ?>
                        <tr>
                            <td><?= ($start + $index + 1); ?></td>
                            <td><?= $member['last_name'] . ', ' . $member['first_name'] . ' ' . $member['middle_name']; ?></td>
                            <td><?= $member['household_number']; ?></td>
                            <td><?= $member['birthdate']; ?></td>
                            <td><?= htmlspecialchars($member['age']); ?></td>
                            <td><?= $member['civil_status']; ?></td>
                            <td><?= $member['gender']; ?></td>
                            <td><?= htmlspecialchars($member['relationship_to_head']); ?></td>
                            <td><?= $member['tribe']; ?></td>
                            <td><?= $member['occupation']; ?></td>
                            <td><?= $member['purok_name']; ?></td>
                            <td>

                                <!-- Edit Button -->
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="<?= $member['member_id'] ?>"
                                    data-first-name="<?= htmlspecialchars($member['first_name']) ?>"
                                    data-last-name="<?= htmlspecialchars($member['last_name']) ?>"
                                    data-middle-name="<?= htmlspecialchars($member['middle_name']) ?>"
                                    data-birthdate="<?= $member['birthdate'] ?>"
                                    data-age="<?= htmlspecialchars($member['age']) ?>"
                                    data-civil-status="<?= $member['civil_status'] ?>"
                                    data-occupation="<?= $member['occupation'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Archive Button -->
                                <button type="button" class="btn btn-sm btn-danger" data-id="<?= $member['member_id'] ?>" data-bs-toggle="modal" data-bs-target="#archiveModal">
                                    <i class="fas fa-archive"></i>
                                </button>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12" class="text-center">No household members found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="household_members.php?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>&purok=<?= htmlspecialchars($filterPurok) ?>&gender=<?= htmlspecialchars($filterGender) ?>&civil_status=<?= htmlspecialchars($filterCivilStatus) ?>&tribe=<?= htmlspecialchars($filterTribe) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <form method="POST" action="cap_add_mem.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMemberModalLabel">Add Household Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="householdHead" class="form-label">Household Head</label>
                        <select name="household_id" id="householdHead" class="form-control">
                            <option value="">-- Select Household Head --</option>
                                <?php
                                    $household_heads = $conn->query("
                                    SELECT h.household_id, h.household_number, hm.last_name, hm.first_name 
                                    FROM household h
                                    JOIN household_members hm ON h.household_head_id = hm.member_id 
                                    WHERE h.archived = 0
                                    ");
                                    
                                    while ($head = $household_heads->fetch_assoc()) {
                                    echo "<option value='{$head['household_id']}'>
                                        {$head['household_number']} - {$head['last_name']}, {$head['first_name']}
                                    </option>";
                                    }
                                ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" name="first_name" id="firstName" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" name="last_name" id="lastName" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="middleName" class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" id="middleName" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="birthdate" class="form-label">Birthdate</label>
                        <input type="date" name="birthdate" id="birthdate" class="form-control" required onchange="calculateMemberAge()">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="civilStatus" class="form-label">Civil Status</label>
                        <select class="form-control" name="civil_status" id="civilStatus" required>
                            <option>-- Select Civil Status --</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Widowed">Widowed</option>
                            <option value="Live In">Live In</option>
                            <option value="Separated">Separated</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select name="gender" id="gender" class="form-control" required>
                            <option>-- Select Gender --</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="relationship_to_head" class="form-label">Relationship to Head</label>
                        <select name="relationship_to_head" id="relationship_to_head" class="form-control" required>
                            <option>-- Select Relationship --</option>
                            <option value="Father">Father</option>
                            <option value="Mother">Mother</option>
                            <option value="Husband">Husband</option>
                            <option value="Wife">Wife</option>
                            <option value="Daughter">Daughter</option>
                            <option value="Son">Son</option>
                            <option value="Sister">Sister</option>
                            <option value="Brother">Brother</option>
                            <option value="Grandmother">Grandmother</option>
                            <option value="Grandfather">Grandfather</option>
                            <option value="Uncle">Uncle</option>
                            <option value="Auntie">Auntie</option>
                            <option value="Counsin">Counsin</option>
                            <option value="In-law">In-law</option>
                            <option value="Friend">Friend</option>
                            <option value="None">None</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tribe" class="form-label">Tribe</label>
                        <select class="form-control" id="tribe" name="tribe" required>
                            <option>-- Select Tribe --</option>
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
                    <div class="col-md-12 mb-3">
                        <label for="occupation" class="form-label">Occupation</label>
                        <select class="form-control" id="occupation" name="occupation" required>
                            <option>-- Select Occupation --</option>
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
                </div>
                <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="purok" class="form-label">Purok</label>
                            <select name="purok_id" id="purok" class="form-control" required>
                                <option value="">-- Select Purok --</option>
                                <?php
                                $puroks = $conn->query("SELECT * FROM puroks");
                                while ($purok = $puroks->fetch_assoc()) {
                                    echo "<option value='{$purok['purok_id']}'>{$purok['purok_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add Member</button>
                </div>
            </form>
            </div>
        </div>
        </div>

        <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="cap_edit_mem.php" method="POST">
                    <input type="hidden" name="member_id" id="editMemberId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Resident</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
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
                <div class="modal-header">
                    <h5 class="modal-title" id="archiveModalLabel">Archive Resident</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to archive this resident?</p>
                    <form id="archiveMemberForm" action="cap_archive_mem.php" method="POST">
                        <input type="hidden" id="archive-member-id" name="member_id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" form="archiveMemberForm">Archive</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        //Edit
        document.addEventListener('DOMContentLoaded', function () {
            const editModal = document.getElementById('editModal');
            editModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget; 
                const memberId = button.getAttribute('data-id'); 

                document.getElementById('editMemberId').value = memberId;
                document.getElementById('editLastName').value = button.getAttribute('data-last-name') || '';
                document.getElementById('editFirstName').value = button.getAttribute('data-first-name') || '';
                document.getElementById('editMiddleName').value = button.getAttribute('data-middle-name') || '';
                document.getElementById('editcivil_status').value = button.getAttribute('data-civil-status') || '';
                document.getElementById('editOccupation').value = button.getAttribute('data-occupation') || ''
            });
        });

        // Archive Modal
        document.addEventListener('DOMContentLoaded', function () {
            const archiveModal = document.getElementById('archiveModal');

            archiveModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget; 
                const memberId = button.getAttribute('data-id'); 
                document.getElementById('archive-member-id').value = memberId; 
            });
        });

        //Age Calculation
        function calculateMemberAge() {
            var birthDateObj = new Date('2000-01-01'); 
            var today = new Date();
            var age = today.getFullYear() - birthDateObj.getFullYear();
            var month = today.getMonth() - birthDateObj.getMonth();
            
            if (month < 0 || (month === 0 && today.getDate() < birthDateObj.getDate())) {
                age--;
            }
            
            document.getElementById('age').value = age;
        }

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
