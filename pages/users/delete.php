<?php
session_start();
include '../../config.php'; 

// Set timezone to Philippine Time
date_default_timezone_set('Asia/Manila');

// Check if the user is logged in and has the correct role (Admin)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../login.php");
    exit();
}


if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $user_id = $_GET['id'];

   
    if ($_SESSION['user_id'] == $user_id) {
        echo "You cannot delete your own account.";
        exit();
    }

   
    $delete_activity_log_query = "DELETE FROM activity_logs WHERE user_id = $1";
    $delete_activity_log_result = pg_query_params($conn, $delete_activity_log_query, array($user_id));

   
    $delete_user_query = "DELETE FROM users WHERE id = $1"; 
    $delete_user_result = pg_query_params($conn, $delete_user_query, array($user_id));

   
    if ($delete_activity_log_result && $delete_user_result) {
        $admin_id = $_SESSION['user_id'];  
        $action = 'delete'; 
        $table_name = 'users';  
        $timestamp = date('Y-m-d H:i:s'); 

        // Fetch the logged-in user's role (Admin)
        $user_type = 'Admin';  

        // Insert the deletion log entry into the activity_logs table
        $log_query = "INSERT INTO activity_logs (user_id, user_type, action, table_name, timestamp) 
                      VALUES ($1, $2, $3, $4, $5)";
        $log_result = pg_query_params($conn, $log_query, array($admin_id, $user_type, $action, $table_name, $timestamp));

        if ($log_result) {
            header("Location: ../dashboard/manage_user.php"); 
            exit();
        } else {
            echo "Failed to log the activity: " . pg_last_error($conn);
        }
    } else {
        echo "Failed to delete user.";
    }
} else {
    echo "Error: Invalid user ID provided.";
}
?>
