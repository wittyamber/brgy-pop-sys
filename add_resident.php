<?php
    include 'side_nav.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add Resident</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>


        <div class="container mt-5">
            <h3>Add New Resident</h3>
            <form action="process_add_resident.php" method="POST">
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                </div>
                <div class="mb-3">
                    <label for="middle_name" class="form-label">Middle Name</label>
                    <input type="text" class="form-control" id="middle_name" name="middle_name" required>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                </div>
                <div class="mb-3">
                    <label for="birthdate">Birthdate</label>
                    <input type="date" id="birthdate" name="birthdate" required oninput="calculateAge()">
                </div>
                <div class="mb-3">
                    <label for="age" class="form-label">Age</label>
                    <input type="number" id="age" name="age" readonly>
                </div>
                <div class="mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <select class="form-control" id="gender" name="gender" required>
                        <option>-select-</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="mb-3">
                                    <label for="address">Address:</label>
                                    <select class="form-control" name="address" id="address" required>
                                        <option>-select-</option>
                                        <option value="Purok 1">Purok 1</option>
                                        <option value="Purok 2">Purok 2</option>
                                        <option value="Purok 3">Purok 3</option>
                                        <option value="Purok 4">Purok 4</option>
                                        <option value="Purok 5">Purok 5</option>
                                        <option value="Purok 6">Purok 6</option>
                                    </select>
                                </div>
                <div class="mb-3">
                    <label for="contact_number" class="form-label">Contact Number</label>
                    <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Resident</button>
            </form>

            <script>
                function calculateAge() {
                    const birthdate = new Date(document.getElementById('birthdate').value);
                    const today = new Date();
                    let age = today.getFullYear() - birthdate.getFullYear();
                    const monthDifference = today.getMonth() - birthdate.getMonth();

                    if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthdate.getDate())) {
                        age--;
                    }
                    document.getElementById('age').value = age;
                }
            </script>
        </div>
    </body>
</html>
