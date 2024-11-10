<?php
    include 'config.php';
    include 'side_nav.php';

    $sql = "SELECT * FROM residents WHERE is_archived = 0 ORDER BY registered_at DESC";
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Resident List</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body> 

        <div class="container mt-5">
            <h3>Resident List</h3>
            <a href="add_resident.php" class="btn btn-primary mb-3">Add New Resident</a>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Birthdate</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Address</th>
                        <th>Contact Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['middle_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                            <td><?php echo $row['birthdate']; ?></td>
                            <td><?php echo $row['age']; ?></td>
                            <td><?php echo $row['gender']; ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                            <td>
                                <!-- Edit Button -->
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">Edit</button>
                                <!-- Archive Button -->
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#archiveModal<?php echo $row['id']; ?>">Archive</button>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Edit Resident</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="process_edit_resident.php" method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <div class="mb-3">
                                                <label for="first_name" class="form-label">First Name</label>
                                                <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($row['first_name']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="middle_name" class="form-label">Middle Name</label>
                                                <input type="text" name="middle_name" class="form-control" value="<?php echo htmlspecialchars($row['middle_name']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="last_name" class="form-label">Last Name</label>
                                                <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($row['last_name']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="birthdate" class="form-label">Birthdate</label>
                                                <input type="date" name="birthdate" class="form-control" value="<?php echo $row['birthdate']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="address" class="form-label">Address</label>
                                                <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($row['address']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="contact_number" class="form-label">Contact Number</label>
                                                <input type="text" name="contact_number" class="form-control" value="<?php echo htmlspecialchars($row['contact_number']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Archive Modal -->
                        <div class="modal fade" id="archiveModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="archiveModalLabel">Archive Resident</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to archive this resident?
                                    </div>
                                    <div class="modal-footer">
                                        <form action="process_archive_resident.php" method="POST">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Archive</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>