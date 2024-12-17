<?php
    include 'config.php';
    include 'side_nav.php'; 
    include 'includes/alerts.php';

    $results_per_page = 10;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $start_limit = ($page - 1) * $results_per_page;

    $search_term = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

    $query = "
        SELECT bo.official_id, bo.name, bo.position, bo.purok_id, bo.contact_number, bo.email, bo.status, bo.date_assigned,
            p.purok_name
        FROM barangay_officials AS bo
        LEFT JOIN puroks AS p ON bo.purok_id = p.purok_id
        WHERE (bo.name LIKE ? OR p.purok_name LIKE ?) 
        LIMIT ?, ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssii', $search_term, $search_term, $start_limit, $results_per_page);
    $stmt->execute();
    $result = $stmt->get_result();
    $barangay_officials = $result->fetch_all(MYSQLI_ASSOC);

    $total_query = "
        SELECT COUNT(*) AS total
        FROM barangay_officials AS bo
        LEFT JOIN puroks AS p ON bo.purok_id = p.purok_id
        WHERE bo.name LIKE ? OR p.purok_name LIKE ?";
    $stmt = $conn->prepare($total_query);
    $stmt->bind_param('ss', $search_term, $search_term);
    $stmt->execute();
    $total_results = $stmt->get_result()->fetch_assoc()['total'];
    $total_pages = ceil($total_results / $results_per_page);

    $puroks = $conn->query("SELECT * FROM puroks")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBPMMS | Barangay Officials</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/household.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center">Barangay Officials</h2>

        <!-- Search Bar and Add Button -->
        <div class="d-flex justify-content-center align-items-center my-4">
            <form class="search-form" method="GET" action="barangay_officials.php">
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

            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOfficialModal">
                <i class="fas fa-user-plus"></i> Add Official
            </button>

        </div>

        <!-- Official List Table -->
        <table class="table table-bordered table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Purok</th>
                    <th>Contact Number</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Date Assigned</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($barangay_officials)): ?>
                <?php foreach ($barangay_officials as $barangay_official): ?>
                    <tr>
                        <td><?= htmlspecialchars($barangay_official['official_id']) ?></td>
                        <td><?= htmlspecialchars($barangay_official['name']) ?></td>
                        <td><?= htmlspecialchars($barangay_official['position']) ?></td>
                        <td><?= htmlspecialchars($barangay_official['purok_name']) ?></td>
                        <td><?= htmlspecialchars($barangay_official['contact_number']) ?></td>
                        <td><?= htmlspecialchars($barangay_official['email']) ?></td>
                        <td><?= htmlspecialchars($barangay_official['status'] ?? 'Active') ?></td>
                        <td><?= htmlspecialchars($barangay_official['date_assigned']) ?></td>
                        <td>   

                            <button 
                                class="btn btn-sm btn-warning edit-btn"
                                data-id="<?= $barangay_official['official_id']; ?>" 
                                data-name="<?= $barangay_official['name']; ?>"
                                data-position="<?= $barangay_official['position']; ?>"
                                data-purok="<?= $barangay_official['purok_id']; ?>"
                                data-contact="<?= $barangay_official['contact_number']; ?>"
                                data-email="<?= $barangay_official['email']; ?>"
                                data-date="<?= $barangay_official['date_assigned']; ?>"
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal">
                                Edit
                            </button>

                            <button 
                                class="btn btn-sm toggle-status-btn <?= ($barangay_official['status'] === 'Inactive') ? 'btn-success' : 'btn-danger' ?>" 
                                data-id="<?= $barangay_official['official_id'] ?>" 
                                data-status="<?= $barangay_official['status'] ?? 'Active' ?>">
                                <?= ($barangay_official['status'] === 'Inactive') ? 'Activate' : 'Deactivate' ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center">No barangay officials found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <!-- Previous Button -->
                <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?= $page - 1; ?>"><i class="fas fa-less-than"></i></a>
                </li>
                
                <!-- Page Numbers -->
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                </li>
                <?php endfor; ?>
                
                <!-- Next Button -->
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?= $page + 1; ?>"><i class="fas fa-greater-than"></i></a>
                </li>
            </ul>
        </nav>


    </div>

        <!-- Add Barangay Officials -->
        <div class="modal fade" id="addOfficialModal" tabindex="-1" aria-labelledby="addOfficialModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOfficialModalLabel">Add Barangay Official</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addOfficialForm" method="POST" action="add_officials.php">
                    <div class="mb-3">
                        <label for="officialName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="officialName" name="officialName" required>
                    </div>
                    <div class="mb-3">
                        <label for="officialPosition" class="form-label">Position</label>
                        <select class="form-control" id="officialPosition" name="officialPosition" required>
                            <option value="">Select Position</option>
                            <option value="Barangay Captain">Barangay Captain</option>
                            <option value="Barangay Kagawad">Barangay Kagawad</option>
                            <option value="Barangay Secretary">Barangay Secretary</option>
                            <option value="Barangay Treasurer">Barangay Treasurer</option>
                            <option value="SK Chairman">SK Chairman</option>
                            <option value="SK Kagawad">SK Kagawad</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="officialPurok" class="form-label">Purok</label>
                        <select class="form-control" id="officialPurok" name="officialPurok" required>
                                <option value="">Select Purok</option>
                                <?php foreach ($puroks as $purok): ?>
                                    <option value="<?= $purok['purok_id']; ?>"><?= $purok['purok_name']; ?></option>
                                <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="officialContactNumber" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="officialContactNumber" name="officialContactNumber" required>
                    </div>
                    <div class="mb-3">
                        <label for="officialEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="officialEmail" name="officialEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="officialDateAssigned" class="form-label">Date Assigned</label>
                        <input type="date" class="form-control" id="officialDateAssigned" name="officialDateAssigned" required>
                    </div>
                        </form>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addOfficialForm" class="btn btn-primary">Add Official</button>
                </div>
                </div>
            </div>
        </div>

        <!-- Edit Barangay Official Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="editForm" action="edit_officials.php" method="POST">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Official</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="official_id" id="official_id">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="position" class="form-label">Position</label>
                                <select class="form-control" id="position" name="position" required autofocus>
                                    <option value="">-select-</option>
                                    <option value="Barangay Captain">Barangay Captain</option>
                                    <option value="Barangay Kagawad">Barangay Kagawad</option>
                                    <option value="Barangay Secretary">Barangay Secretary</option>
                                    <option value="Barangay Treasurer">Barangay Treasurer</option>
                                    <option value="SK Chairman">SK Chairman</option>
                                    <option value="SK Kagawad">SK Kagawad</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="purok" class="form-label">Purok</label>
                                <select class="form-control" id="purok" name="purok" required autofocus>
                                    <option value="">Select Purok</option>
                                    <?php foreach ($puroks as $purok): ?>
                                        <option value="<?= $purok['purok_id']; ?>"><?= $purok['purok_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contact_number" name="contact_number" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="date_assigned" class="form-label">Date Assigned</label>
                                <input type="date" class="form-control" id="date_assigned" name="date_assigned" required autofocus>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Activate/Deactivate Modal -->
        <div class="modal fade" id="statusModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="statusModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="statusModalBody"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmStatusBtn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>

        


    <!-- Include Chart.js and other JS libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Include Bootstrap JS (requires Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
         // Add
        document.getElementById('addOfficialForm').addEventListener('submit', function (e) {
            e.preventDefault();

            var modal = new bootstrap.Modal(document.getElementById('addOfficialModal'));
            modal.show();

            const formData = new FormData(this);

            fetch('add_officials.php', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.text())
                .then(data => {
                    alert(data); 
                    location.reload(); 
                })
                .catch(error => console.error('Error adding official:', error));
        });

        // Edit
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                // Populate the modal fields with data attributes
                document.getElementById('official_id').value = this.getAttribute('data-id');
                document.getElementById('name').value = this.getAttribute('data-name');
                document.getElementById('position').value = this.getAttribute('data-position');
                document.getElementById('purok').value = this.getAttribute('data-purok');
                document.getElementById('contact_number').value = this.getAttribute('data-contact');
                document.getElementById('email').value = this.getAttribute('data-email');
                document.getElementById('date_assigned').value = this.getAttribute('data-date');
            });
        });

        // View official details
        document.querySelectorAll(".view-btn").forEach(button => {
            button.addEventListener("click", function () {
                const officialId = this.dataset.id;

                fetch(`get_official.php?id=${officialId}`)
                    .then(response => response.json())
                        .then(data => {
                            document.getElementById("viewName").textContent = data.name;
                            document.getElementById("viewPosition").textContent = data.position;
                            document.getElementById("viewAddress").textContent = data.address;
                            document.getElementById("viewContact").textContent = data.contact_number;
                            document.getElementById("viewEmail").textContent = data.email;
                            document.getElementById("viewStatus").textContent = data.status;

                            // Show modal
                            const modal = new bootstrap.Modal(document.getElementById("viewOfficialModal"));
                            modal.show();
                        });
                });
            });

            document.querySelectorAll(".edit-btn").forEach(button => {
                button.addEventListener("click", function () {
                    const officialId = this.dataset.id;

                    fetch(`get_official.php?id=${officialId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error("Failed to fetch official data");
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.error) {
                                alert(data.error);
                                return;
                            }

                            document.getElementById("official_id").value = data.official_id;
                            document.getElementById("name").value = data.name;
                            document.getElementById("position").value = data.position;
                            document.getElementById("purok").value = data.purok;
                            document.getElementById("contact_number").value = data.contact_number;
                            document.getElementById("email").value = data.email;
                            document.getElementById("date_assigned").value = data.date_assigned;

                            // Show modal
                            const modal = new bootstrap.Modal(document.getElementById("editModal"));
                            modal.show();
                        })
                        .catch(error => {
                            console.error("Error fetching data:", error);
                            alert("An error occurred while fetching official details.");
                        });
                });
            });


            // Activate/Deactivate official
            document.querySelectorAll(".toggle-status-btn").forEach(button => {
                button.addEventListener("click", function () {
                    const officialId = this.dataset.id;
                    const currentStatus = this.dataset.status;
                    const newStatus = currentStatus === "Active" ? "Inactive" : "Active";

                    document.getElementById("statusModalLabel").textContent = `${newStatus} Barangay Official`;
                    document.getElementById("statusModalBody").textContent = `Are you sure you want to ${newStatus.toLowerCase()} this official?`;

                    const confirmBtn = document.getElementById("confirmStatusBtn");
                    confirmBtn.onclick = function () {
                        fetch(`toggle_status_official.php?id=${officialId}&status=${newStatus}`)
                            .then(response => response.text())
                            .then(data => {
                                alert(data); // Show success or error message
                                location.reload(); // Reload the page
                            });
                    };

                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById("statusModal"));
                    modal.show();
                });
            });
        });

        //Pagination
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const url = this.getAttribute('href');

                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        document.querySelector('#officials-container').innerHTML = html;
                    })
                    .catch(error => console.error('Error loading page:', error));
            });
        });


    </script>

</body>
</html>
