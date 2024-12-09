<?php
    require 'config.php'; 
    include 'side_nav.php';
    include 'includes/alerts.php';

    // Pagination variables
    $limit = 10; // Records per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($page - 1) * $limit;

    // Search functionality
    $search = $_GET['search'] ?? ''; // Get the search query
    $searchCondition = !empty($search) ? "AND (hm.first_name LIKE ? OR hm.last_name LIKE ? OR hm.relationship_to_head LIKE ?)" : '';

    // SQL query to fetch members and their household head with optional search
    $sql = "SELECT hm.*, h.last_name AS head_last_name, h.first_name AS head_first_name 
            FROM household_members hm 
            JOIN households h ON hm.household_id = h.household_id 
            WHERE hm.archived = 0 $searchCondition 
            ORDER BY hm.last_name ASC 
            LIMIT ?, ?";
    $stmt = $conn->prepare($sql);

    // Bind parameters based on whether a search query exists
    if (!empty($search)) {
        $searchTerm = '%' . $search . '%';
        $stmt->bind_param("sssii", $searchTerm, $searchTerm, $searchTerm, $start, $limit);
    } else {
        $stmt->bind_param("ii", $start, $limit);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $members = $result->fetch_all(MYSQLI_ASSOC);

    // Count total records for pagination
    $countSql = "SELECT COUNT(*) AS total 
                FROM household_members hm 
                JOIN households h ON hm.household_id = h.household_id 
                WHERE hm.archived = 0 $searchCondition";
    $countStmt = $conn->prepare($countSql);

    if (!empty($search)) {
        $countStmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    }

    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $total_rows = $countResult->fetch_assoc()['total'];
    $total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBPMMS | Residents</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/household.css">
</head>
<body>
    <div class="container mt-5">

        <h2 class="text-center">Residents</h2>

        <!-- Search Bar and Add Button -->
        <div class="d-flex justify-content-center align-items-center my-4">

            <form class="search-form" method="GET" action="household_members.php">
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

            <button class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                <i class="fas fa-user-plus"></i> Add Resident
            </button>
        </div>

        <!-- Members Table -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Household Head</th>
                    <th>Birthdate</th>
                    <th>Age</th>
                    <th>Civil Status</th>
                    <th>Gender</th>
                    <th>Relationship to the Head</th>
                    <th>Tribe</th>
                    <th>Occupation</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($members)): ?>
                    <?php foreach ($members as $index => $member): ?>
                        <tr>
                            <td><?= ($start + $index + 1); ?></td>
                            <td><?= $member['last_name'] . ', ' . $member['first_name'] . ' ' . $member['middle_name']; ?></td>
                            <td><?= $member['head_last_name'] . ', ' . $member['head_first_name'] ?></td> <!-- Household Head's Full Name -->
                            <td><?= $member['birthdate']; ?></td>
                            <td><?= htmlspecialchars($member['age']) ?></td>
                            <td><?= $member['civil_status']; ?></td>
                            <td><?= $member['gender']; ?></td>
                            <td><?= htmlspecialchars($member['relationship_to_head']) ?></td>
                            <td><?= $member['tribe']; ?></td>
                            <td><?= $member['occupation']; ?></td>
                            <td>

                                <!-- Edit Button -->
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="<?= $member['member_id'] ?>"
                                    data-first-name="<?= htmlspecialchars($member['first_name']) ?>"
                                    data-last-name="<?= htmlspecialchars($member['last_name']) ?>"
                                    data-middle-name="<?= htmlspecialchars($member['middle_name']) ?>"                                   
                                    data-civil-status="<?= $member['civil_status'] ?>"
                                    data-occupation="<?= $member['occupation'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Archive Button -->
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#archiveModal"
                                    data-id="<?= $member['member_id'] ?>">
                                    <i class="fas fa-archive"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11" class="text-center">No household members found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="household_members.php?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="add_member.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMemberModalLabel">Add Household Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="householdHead" class="form-label">Household Head</label>
                            <select name="household_id" id="householdHead" class="form-control" required>
                                <option value="">-- Select Household Head --</option>
                                <?php
                                // Fetch household heads dynamically
                                $household_heads = $conn->query("SELECT household_id, last_name, first_name FROM households WHERE archived = 0");
                                while ($head = $household_heads->fetch_assoc()) {
                                    echo "<option value='{$head['household_id']}'>{$head['last_name']}, {$head['first_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" name="first_name" id="firstName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" name="last_name" id="lastName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="middleName" class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" id="middleName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="birthdate" class="form-label">Birthdate</label>
                            <input type="date" name="birthdate" id="birthdate" class="form-control" required onchange="calculateMemberAge()">
                        </div>
                        <div class="mb-3">
                            <label for="age" class="form-label">Age</label>
                            <input type="number" name="age" id="age" class="form-control" readonly>
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
                            <select name="gender" id="gender" class="form-control" required>
                                <option>-select-</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="relationship_to_head" class="form-label">Relationship to Head</label>
                            <select name="rrelationship_to_head" id="relationship_to_head" class="form-control" required>
                                <option>-select-</option>
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
                <form action="edit_member.php" method="POST">
                    <input type="hidden" name="member_id" id="editMemberId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Resident</h5>
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
                <form action="archive_member.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="archiveModalLabel">Archive Resident</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to archive this resident?
                        <input type="hidden" id="archive-member-id" name="member_id">
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
                const memberId = button.getAttribute('data-id'); // Get the member_id

                // Populate the hidden input and other fields via AJAX or directly from the button's data attributes
                document.getElementById('editMemberId').value = memberId;
                document.getElementById('editLastName').value = button.getAttribute('data-last-name') || '';
                document.getElementById('editFirstName').value = button.getAttribute('data-first-name') || '';
                document.getElementById('editMiddleName').value = button.getAttribute('data-middle-name') || '';
                document.getElementById('editcivil_status').value = button.getAttribute('data-civil-status') || '';
                document.getElementById('editOccupation').value = button.getAttribute('data-occupation') || ''
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Archive Modal Event Listener
            const archiveModal = document.getElementById('archiveModal');
            archiveModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const memberId = button.getAttribute('data-id');
                document.getElementById('archive-member-id').value = memberId;
            });
        });

        //Age Calculation
        function calculateMemberAge() {
            const birthdate = document.getElementById('birthdate').value;
            if (birthdate) {
                const today = new Date();
                const birthDate = new Date(birthdate);
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                document.getElementById('age').value = age;
            } else {
                document.getElementById('age').value = '';
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
