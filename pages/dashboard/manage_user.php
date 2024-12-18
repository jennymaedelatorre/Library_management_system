<?php
session_start();
include '../../config.php';  // Correct path to config.php

// Check if the user is logged in and has the correct role (Admin)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Internal CSS -->
    <style>
        /* Custom CSS */
        .container-fluid {
            display: flex;
        }

        h2 {
            font-family: Georgia, 'Times New Roman', Times, serif;
            font-size: 1rem;
            font-weight: bold;
            margin-left: 20px;
            font-style: italic;
        }

        .content {
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            margin-left: 260px;
            padding: 20px;
            width: calc(100% - 260px);
        }

        .btn {
            padding: 8px 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .btn:hover {
            background: rgb(14, 125, 20);
            color: white;
        }

        .add-btn {
            font-size: 1rem;
            text-decoration: none;
            padding: 3px 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }

        .add-btn:hover {
            transform: scale(1.05);
        }

        .delete-btn {
            padding: 10px 12px;
            background: rgb(213, 53, 44);
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
        }

        .delete-btn:hover {
            background: rgb(196, 19, 19);
        }
    </style>
</head>

<body>
    <?php include '../../templates/navbar.php'; ?> 

    <div class="container-fluid">
        <div class="content mt-3">
            <h1 class="mb-3" style="font-weight: bolder; font-family: Georgia, 'Times New Roman', Times, serif; color:#4CAF50;">
                Manage All Users
            </h1>
            <hr>


            <style>
                .user-table th,
                .user-table td {
                    text-align: center;
                    vertical-align: middle;
                }

                .user-table th {
                    background-color: #f5f5f5;
                    font-weight: bold;
                }
            </style>

            <!-- User Management -->
            <div class="d-flex align-items-center mt-4 mb-2 ms-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add New User</button>
            </div>

            <!-- Table for Admins -->
            <h2 class="mt-5">List of All Admins</h2>
            <table class="table table-bordered user-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">User ID</th>
                        <th style="width: 30%;">Name</th>
                        <th style="width: 40%;">Email</th>
                        <th style="width: 20%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $admin_query = "SELECT id, name, email, role FROM users WHERE role = 1";
                    $admin_result = pg_query($conn, $admin_query);

                    if ($admin_result && pg_num_rows($admin_result) > 0) {
                        while ($admin = pg_fetch_assoc($admin_result)) {
                            echo "<tr>
                        <td>{$admin['id']}</td>
                        <td>{$admin['name']}</td>
                        <td>{$admin['email']}</td>
                        <td>
                            <button class='btn btn-warning' data-bs-toggle='modal' data-bs-target='#editUserModal' 
                                data-id='{$admin['id']}' 
                                data-name='{$admin['name']}' 
                                data-email='{$admin['email']}' 
                                data-role='{$admin['role']}'>Edit</button>
                        </td>
                    </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No admins found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Table for Librarians -->
            <h2 class="mt-5">List of All Librarians</h2>
            <table class="table table-bordered user-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">User ID</th>
                        <th style="width: 30%;">Name</th>
                        <th style="width: 40%;">Email</th>
                        <th style="width: 20%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $librarian_query = "SELECT id, name, email, role FROM users WHERE role = 2"; 
                    $librarian_result = pg_query($conn, $librarian_query);

                    if ($librarian_result && pg_num_rows($librarian_result) > 0) {
                        while ($librarian = pg_fetch_assoc($librarian_result)) {
                            echo "<tr>
                        <td>{$librarian['id']}</td>
                        <td>{$librarian['name']}</td>
                        <td>{$librarian['email']}</td>
                        <td>
                            <button class='btn btn-warning' data-bs-toggle='modal' data-bs-target='#editUserModal' 
                                data-id='{$librarian['id']}' 
                                data-name='{$librarian['name']}' 
                                data-email='{$librarian['email']}' 
                                data-role='{$librarian['role']}'>Edit</button>
                             <button class='delete-btn btn-danger' data-id='{$librarian['id']}' data-bs-toggle='modal' data-bs-target='#deleteModal'>Delete</button>
                        </td>
                    </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No librarians found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <h2 class="mt-5">List of All Students</h2>
<table class="table table-bordered user-table">
    <thead>
        <tr>
            <th style="width: 10%;">User ID</th>
            <th style="width: 25%;">Name</th>
            <th style="width: 30%;">Email</th>
            <th style="width: 20%;">Total Borrowed Books</th>
            <th style="width: 15%;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Modify the query to include total_borrowed_books from the user_activity_summary view
        $student_query = "
            SELECT u.id, u.name, u.email, u.role, uas.total_borrowed_books
            FROM users u
            LEFT JOIN user_activity_summary uas ON u.id = uas.user_id
            WHERE u.role = 3"; // Fetch only students (assuming role 3 is student)

        // Execute the query
        $student_result = pg_query($conn, $student_query);

        if ($student_result && pg_num_rows($student_result) > 0) {
            while ($student = pg_fetch_assoc($student_result)) {
                // Fetch the data from the result and display it
                echo "<tr>
                        <td>{$student['id']}</td>
                        <td>{$student['name']}</td>
                        <td>{$student['email']}</td>
                        <td>{$student['total_borrowed_books']}</td> <!-- Display total borrowed books -->
                        <td>
                            <button class='btn btn-warning' data-bs-toggle='modal' data-bs-target='#editUserModal' 
                                data-id='{$student['id']}' 
                                data-name='{$student['name']}' 
                                data-email='{$student['email']}' 
                                data-role='{$student['role']}'>Edit</button>
                             <button class='delete-btn btn-danger' data-id='{$student['id']}' data-bs-toggle='modal' data-bs-target='#deleteModal'>Delete</button>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No students found.</td></tr>";
        }
        ?>
    </tbody>
</table>

        </div>

        <!-- Modal for Adding a New User -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="../users/add.php">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-control" id="role" name="role" required>
                                    <option value="1">Admin</option>
                                    <option value="2">Librarian</option>
                                    <option value="3">Student</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Add User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <!-- Modal for editing a user -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="../users/edit.php" method="POST">
                            <input type="hidden" name="user_id" id="user_id">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="userName" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="userEmail" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="userRole" name="role" required>
                                    <option value="1">Admin</option>
                                    <option value="2">Librarian</option>
                                    <option value="3">Student</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Deletion Confirmation -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this user?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <a href="#" id="deleteUserBtn" class="delete-btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>




        <!-- Bootstrap JS & Popper.js -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var editButtons = document.querySelectorAll('button[data-bs-toggle="modal"]');

                editButtons.forEach(function(button) {
                    button.addEventListener('click', function() {
                        var userId = button.getAttribute('data-id');
                        var userName = button.getAttribute('data-name');
                        var userEmail = button.getAttribute('data-email');
                        var userRole = button.getAttribute('data-role');

            
                        document.getElementById('user_id').value = userId;
                        document.getElementById('userName').value = userName;
                        document.getElementById('userEmail').value = userEmail;
                        document.getElementById('userRole').value = userRole;
                    });
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
            
                const deleteButtons = document.querySelectorAll('.delete-btn');

             
                deleteButtons.forEach(function(button) {
                    button.addEventListener('click', function() {
                       
                        const userId = button.getAttribute('data-id');

                    
                        const deleteUrl = '../users/delete.php?id=' + userId;

                
                        document.getElementById('deleteUserBtn').setAttribute('href', deleteUrl);
                    });
                });
            });
        </script>


</body>

</html>