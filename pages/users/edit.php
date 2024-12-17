<?php
session_start();
include '../../config.php'; 

// Set timezone to Philippine Time
date_default_timezone_set('Asia/Manila');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['user_id'], $_POST['name'], $_POST['email'], $_POST['role'])) {
        $user_id = $_POST['user_id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $role = $_POST['role'];

        if ($_SESSION['user_id'] == $user_id && $role != 1) {
            echo "You cannot change your own role.";
            exit();
        }

        $query = "UPDATE users SET name = $1, email = $2, role = $3 WHERE id = $4";
        $result = pg_query_params($conn, $query, array($name, $email, $role, $user_id));

        if ($result) {
            $admin_id = $_SESSION['user_id']; 
            $action = 'edit'; 
            $table_name = 'users'; 
            $timestamp = date('Y-m-d H:i:s');  

            // Fetch the logged-in user's role (admin)
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

            // Check if the log was successfully added
            if ($log_result) {
                header("Location: ../dashboard/manage_user.php"); 
                exit();
            } else {
                echo "Failed to log the activity: " . pg_last_error($conn);
            }
        } else {
            echo "Failed to update user.";
        }
    } else {
        echo "Error: Missing form data.";
    }
}
?>
