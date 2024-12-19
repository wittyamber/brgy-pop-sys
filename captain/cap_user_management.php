<?php
    session_start();
    include '../config.php';
    include 'side_navigation.php';
    include '../includes/alerts.php';

    // Pagination settings
    $limit = 10; // Number of users per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Search logic
    $searchQuery = isset($_GET['query']) ? $_GET['query'] : '';

    // Fetch users from the database (sorted alphabetically by name)
    if ($searchQuery) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE name LIKE ? OR username LIKE ? ORDER BY name ASC LIMIT ? OFFSET ?");
        $searchTerm = "%$searchQuery%";
        $stmt->bind_param("ssii", $searchTerm, $searchTerm, $limit, $offset);
    } else {
        $stmt = $conn->prepare("SELECT * FROM users ORDER BY name ASC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);

    // Count total number of users for pagination
    if ($searchQuery) {
        $totalResult = $conn->query("SELECT COUNT(*) AS total FROM users WHERE name LIKE ? OR username LIKE ?", "ss", ["%$searchQuery%", "%$searchQuery%"]);
    } else {
        $totalResult = $conn->query("SELECT COUNT(*) AS total FROM users");
    }
    
    $totalUsers = $totalResult->fetch_assoc()['total'];
    $totalPages = ceil($totalUsers / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBPMMS | User Management</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../css/household.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">User Management</h2>

        <!-- Search Bar and Add Button -->
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchUser" placeholder="Search user by name..." />
                        <button type="button" class="btn btn-primary" id="searchBtn">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                        <a href="cap_user_management.php" class="btn btn-secondary">
                            <i class="fas fa-undo me-1"></i> Reset Filter
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-plus"></i> Add New User
            </button>
        </div>

        <table class="table table-bordered table-striped mt-4">
            <thead>
            <tr>
                <th>No.</th>
                <th>Name</th>
                <th>Username</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $index => $user): ?>
                <tr>
                    <td><?= $offset + $index + 1 ?></td> 
                    <td><?= $user['name'] ?></td>
                    <td><?= $user['username'] ?></td>
                    <td><?= $user['role'] ?></td>
                    <td><?= $user['status'] ?? 'Active' ?></td>
                    <td>
                        <button class="btn btn-info btn-sm view-btn" data-id="<?= $user['id'] ?>"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-warning btn-sm edit-btn" data-id="<?= $user['id'] ?>"><i class="fas fa-edit"></i></button>
                        <button 
                            class="btn btn-sm toggle-status-btn <?= ($user['status'] === 'Inactive') ? 'btn-success' : 'btn-danger' ?>" 
                            data-id="<?= $user['id'] ?>" 
                            data-status="<?= $user['status'] ?? 'Active' ?>">
                            <?= ($user['status'] === 'Inactive') ? 'Activate' : 'Deactivate' ?>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="cap_user_management.php?page=<?= $page - 1 ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="cap_user_management.php?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="cap_user_management.php?page=<?= $page + 1 ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <!-- Add User Modal -->
    <div class="modal" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                    <div class="form-group">
                            <label for="add-name">Name</label>
                            <input type="text" class="form-control" name="name" id="add-name" required>
                        </div>
                        <div class="form-group">
                            <label for="add-username">Username</label>
                            <input type="text" class="form-control" name="username" id="add-username" required>
                        </div>
                        <div class="form-group">
                            <label for="add-password">Password</label>
                            <input type="password" class="form-control" name="password" id="add-password" required>
                        </div>
                        <div class="form-group">
                            <label for="add-role">Role</label>
                            <select class="form-control" name="role" id="add-role" required>
                                <option value="Captain">Captain</option>
                                <option value="Secretary">Secretary</option>
                                <option value="BHW">BHW</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Add User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal" id="viewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewDetails">
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="form-group">
                            <label for="edit-name">Name</label>
                            <input type="text" class="form-control" name="name" id="edit-name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-username">Username</label>
                            <input type="text" class="form-control" name="username" id="edit-username" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-role">Role</label>
                            <select class="form-control" name="role" id="edit-role" required>
                                <option value="Captain">Captain</option>
                                <option value="Secretary">Secretary</option>
                                <option value="Staff">Staff</option>
                                <option value="BHW">BHW</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        document.getElementById('searchBtn').addEventListener('click', function () {
            const searchQuery = document.getElementById('searchUser').value;
            const url = new URL(window.location.href);
            url.searchParams.set('query', searchQuery); 

            window.location.href = url.toString(); 
        });
    

        // Add
        document.getElementById('addUserForm').addEventListener('submit', (e) => {
            e.preventDefault();

            fetch('cap_add_user.php', {
                method: 'POST',
                body: new FormData(e.target),
            })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    location.reload();
                })
                .catch(error => console.error('Error adding user:', error));
        });


        // JavaScript to handle view, edit, and toggle status actions
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.view-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const userId = button.getAttribute('data-id');
                    fetch(`cap_view_user.php?id=${userId}`)
                        .then(response => response.text())
                        .then(data => {
                            document.getElementById('viewDetails').innerHTML = data;
                            const viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
                            viewModal.show();
                        })
                        .catch(error => console.error('Error fetching user details:', error));
                });
            });

            // Edit Button Click
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const userId = button.getAttribute('data-id');
                    fetch(`cap_get_user.php?id=${userId}`)
                        .then(response => response.json())
                        .then(user => {
                            document.getElementById('edit-id').value = user.id;
                            document.getElementById('edit-name').value = user.rname;
                            document.getElementById('edit-username').value = user.username;
                            document.getElementById('edit-role').value = user.role;
                            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                            editModal.show();
                        })
                        .catch(error => console.error('Error fetching user for edit:', error));
                });
            });

            // Edit Form Submission
            document.getElementById('editForm').addEventListener('submit', (e) => {
                e.preventDefault();
                fetch('cap_edit_user.php', {
                    method: 'POST',
                    body: new FormData(e.target)
                })
                .then(response => {
                    if (response.ok) {
                        location.reload(); // Reload the page after a successful update
                    } else {
                        alert('Error updating user!');
                    }
                })
                .catch(error => console.error('Error updating user:', error));
            });
        });

        // Search
        document.getElementById('searchUser').addEventListener('input', (e) => {
            const query = e.target.value;

            fetch(`cap_search_user.php?query=${query}`)
                .then(response => response.json())
                .then(users => {
                    const tableBody = document.querySelector('#userTable tbody');
                    tableBody.innerHTML = ''; // Clear existing rows

                    users.forEach(user => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${user.id}</td>
                            <td>${user.name}</td>
                            <td>${user.username}</td>
                            <td>${user.role}</td>
                            <td>${user.status}</td>
                            <td>
                                <button class="btn btn-info btn-sm view-btn" data-id="<?= $user['id'] ?>"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-warning btn-sm edit-btn" data-id="<?= $user['id'] ?>"><i class="fas fa-edit"></i></button>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                })
                .catch(error => console.error('Error searching users:', error));
        });


        document.querySelectorAll('.toggle-status-btn').forEach(button => {
            button.addEventListener('click', () => {
                const userId = button.getAttribute('data-id');
                const status = button.getAttribute('data-status') === 'Active' ? 'Inactive' : 'Active';
                fetch(`cap_toggle_status.php?id=${userId}&status=${status}`)
                    .then(() => location.reload());
            });
        });
    </script>
</body>
</html>
