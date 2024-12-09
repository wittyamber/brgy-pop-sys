<?php
    session_start();
    include 'config.php';
    include 'side_nav.php';
    include 'includes/alerts.php';

    // Fetch users from the database
    $result = $conn->query("SELECT * FROM users");
    $users = $result->fetch_all(MYSQLI_ASSOC);
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
    <link rel="stylesheet" href="css/household.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">User Management</h2>

        <!-- Search Bar and Add Button -->
        <div class="d-flex justify-content-center align-items-center my-4">

            <div class="input-group">
                <input type="text" class="form-control me-2" id="searchUser" placeholder="Search user by username...">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add New User</button>

        <table class="table table-bordered table-striped mt-4">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= $user['name'] ?></td>
                    <td><?= $user['username'] ?></td>
                    <td><?= $user['role'] ?></td>
                    <td><?= $user['status'] ?? 'Active' ?></td>
                    <td>
                        <button class="btn btn-info btn-sm view-btn" data-id="<?= $user['id'] ?>"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-warning btn-sm edit-btn" data-id="<?= $user['id'] ?>"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-danger btn-sm toggle-status-btn" 
                                data-id="<?= $user['id'] ?>" 
                                data-status="<?= $user['status'] ?? 'Active' ?>">
                            <?= ($user['status'] === 'Inactive') ? 'Activate' : 'Deactivate' ?>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
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
                                <option value="Staff">Staff</option>
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
                    <!-- Content dynamically loaded via JS -->
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

        // Add
        document.getElementById('addUserForm').addEventListener('submit', (e) => {
            e.preventDefault();

            fetch('add_user.php', {
                method: 'POST',
                body: new FormData(e.target),
            })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    location.reload(); // Reload to reflect new user
                })
                .catch(error => console.error('Error adding user:', error));
        });


        // JavaScript to handle view, edit, and toggle status actions
        document.addEventListener('DOMContentLoaded', () => {
            // View Button Click
            document.querySelectorAll('.view-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const userId = button.getAttribute('data-id');
                    fetch(`view_user.php?id=${userId}`)
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
                    fetch(`get_user.php?id=${userId}`)
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
                fetch('edit_user.php', {
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

            fetch(`search_user.php?query=${query}`)
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
                fetch(`toggle_status.php?id=${userId}&status=${status}`)
                    .then(() => location.reload());
            });
        });
    </script>
</body>
</html>
