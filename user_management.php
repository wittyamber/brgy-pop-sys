<?php
    include 'side_nav.php';
    include 'config.php';
    
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBPMMS | User Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/management_user.css">
    
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>User Management</h1>
            <a href="#" class="add-btn" onclick="event.preventDefault(); openModal();">
    <i class="fas fa-plus"></i> Add User
</a>

        </div>
        <div class="search-container">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="text" name="searchTerm" placeholder="Search..." value="<?php echo isset($searchTerm) ? $searchTerm : ''; ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <?php
    if (isset($result) && $result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>".$row["id"]."</td><td>".$row["name"]."</td><td>".$row["email"]."</td></tr>";
        }
        echo "</table>";
    } else {
        
    }
    ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>{$row['id']}</td>";
                    echo "<td>{$row['name']}</td>";
                    echo "<td>{$row['role']}</td>";
                    echo "<td>{$row['status']}</td>";
                    echo "<td>
                            <a href='edit_user.php?id={$row['id']}' class='action-btn edit-btn'>
                                <i class='fas fa-edit'></i>
                            </a>
                            <a href='delete_user.php?id={$row['id']}' class='action-btn delete-btn' onclick='return confirm(\"Are you sure?\");'>
                                <i class='fas fa-trash'></i>
                            </a>
                        </td>";
                    echo "</tr>";   
                }
                ?>
                </tbody>
            </table>
        </div>
    </table>
    <form method="POST" action="save_user.php">
    <div>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div>
        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="Admin">Admin</option>
            <option value="Staff">Staff</option>
        </select>
    </div>
    <div>
        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-success">Save User</button>
    <button type="button" class="btn btn-danger" onclick="closeModal()">Cancel</button>
</form>

<!-- </div>

    Add User Modal
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Add New User</h2>
            <form>
                <div>
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name">
                </div>
                <div>
                    <label for="role">Role:</label>
                    <select id="role" name="role">
                        <option value="Admin">Admin</option>
                        <option value="Staff">Staff</option>
                    </select>
                </div>
                <div>
                    <label for="status">Status:</label>
                    <select id="status" name="status">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <div>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password">
                </div>
                
                <button type="button" class="btn btn-success">Save User</button>
                <button type="button" class="btn btn-danger" onclick="closeModal()">Cancel</button>

            </form>
        </div>
    </div> -->


    <script>
        // Get modal elements
        const modal = document.getElementById("addUserModal");
        const closeModalButton = document.querySelector(".close");

        // Open modal
        function openModal() {
            modal.style.display = "block";
        }

        // Close modal
        function closeModal() {
            modal.style.display = "none";
        }

        // Close modal when clicking the "X" button
        closeModalButton.addEventListener("click", closeModal);

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        };

        function openModal() {
            document.getElementById("addUserModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("addUserModal").style.display = "none";
        }

    </script> -->
</body>
</html>
