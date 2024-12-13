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

        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background: #4CAF50;
            padding-top: 20px;
        }

        .sidebar a {
            color: white;
            padding: 10px;
            text-decoration: none;
            display: block;
            margin: 5px 0;
        }

        .sidebar a:hover {
            background: #45a049;
            color: white;
        }

        .content {
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
            background: #45a049;
        }
    </style>
</head>
<body>
<?php include '../../templates/navbar.php'; ?> <!-- Include Sidebar Navigation -->

<div class="container-fluid">

    <!-- Content Area -->
    <div class="content">
        <h1>Welcome, Admin!</h1>
        <p>Use the links in the sidebar to manage users, view reports, or adjust settings.</p>

        <h2>User Management</h2>
        <p>View, edit, or delete user accounts.</p>

        <h2>System Reports</h2>
        <p>View system activity and performance reports.</p>

        <h2>All Users</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query to fetch users from the database
                $query = "SELECT id, name, email, role FROM users";
                $result = pg_query($conn, $query); // Execute the query

                // Check if there are any results
                if ($result) {
                    while ($row = pg_fetch_assoc($result)) {
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['email']}</td>
                            <td>" . ($row['role'] == 1 ? "Admin" : ($row['role'] == 2 ? "Librarian" : "Student")) . "</td>
                            <td>
                                <a href='edit_user.php?id={$row['id']}' class='btn'>Edit</a>
                                <a href='../actions/delete_user_action.php?id={$row['id']}' class='btn' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No users found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap JS & Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
