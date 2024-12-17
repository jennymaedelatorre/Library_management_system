<?php
session_start();
include '../../config.php'; // Database connection

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
        $admin_id = $_SESSION['user_id'];  
        $action = 'add';  
        $table_name = 'users'; 
        $timestamp = date('Y-m-d H:i:s'); 

        // Fetch the logged-in admin's role
        $admin_type_query = "SELECT role FROM users WHERE id = $1";
        $admin_type_result = pg_query_params($conn, $admin_type_query, array($admin_id));

        if ($admin_type_result && pg_num_rows($admin_type_result) > 0) {
            $admin_data = pg_fetch_assoc($admin_type_result);
            $user_type = '';
            switch ($admin_data['role']) {
                case 1:
                    $user_type = 'Admin';
                    break;
                case 2:
                    $user_type = 'Librarian';
                    break;
                case 3:
                    $user_type = 'Student';
                    break;
                default:
                    $user_type = 'Unknown';
            }
        }

        // Insert log into the activity_logs table
        $log_query = "INSERT INTO activity_logs (user_id, user_type, action, table_name, timestamp) 
                      VALUES ($1, $2, $3, $4, $5)";
        $log_result = pg_query_params($conn, $log_query, array($admin_id, $user_type, $action, $table_name, $timestamp));

      
        if ($log_result) {
            $_SESSION['success'] = 'User added successfully!';
        } else {
            $_SESSION['error'] = 'Failed to log the activity: ' . pg_last_error($conn);
        }

        header("Location: ../dashboard/manage_user.php");
    } else {
        $_SESSION['error'] = 'Failed to add user: ' . pg_last_error($conn);
        header("Location: ../dashboard/manage_user.php");
    }
    exit();
}
?>
