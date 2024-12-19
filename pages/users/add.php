<?php
session_start();
include '../../config.php'; 

date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $query = "INSERT INTO users (name, email, password, role) VALUES ($1, $2, $3, $4)";
    $result = pg_query_params($conn, $query, array($name, $email, $hashed_password, $role));

    if ($result) {
        $_SESSION['success'] = 'User added successfully!';
        header("Location: ../dashboard/manage_user.php");
    } else {
        $_SESSION['error'] = 'Failed to add user: ' . pg_last_error($conn);
        header("Location: ../dashboard/manage_user.php");
    }
    exit();
}
?>
