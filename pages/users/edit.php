<?php
session_start();
include '../../config.php'; 

// Set timezone to Philippine Time
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['user_id'], $_POST['name'], $_POST['email'])) {
        $user_id = $_POST['user_id'];
        $name = $_POST['name'];
        $email = $_POST['email'];

    
        if ($_SESSION['user_id'] == $user_id && $_SESSION['role'] != 1) {
            echo "You cannot change your own role.";
            exit();
        }

        
        $query = "UPDATE users SET name = $1, email = $2 WHERE id = $3";
        $result = pg_query_params($conn, $query, array($name, $email, $user_id));

        if ($result) {
            header("Location: ../dashboard/manage_user.php"); 
            exit();
        } else {
            echo "Failed to update user.";
        }
    } else {
        echo "Error: Missing form data.";
    }
}
?>
