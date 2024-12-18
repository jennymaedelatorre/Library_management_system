<?php
session_start();
include '../../config.php';

// Check if the user is logged in and has the correct role (Admin or Librarian)
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: ../login.php");
    exit;
}

date_default_timezone_set('Asia/Manila');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    var_dump($_POST); 
    
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $author = isset($_POST['author']) ? trim($_POST['author']) : '';
    $genre = isset($_POST['genre']) ? trim($_POST['genre']) : '';
    $available = isset($_POST['available']) ? 'TRUE' : 'FALSE';

    // Basic validation to ensure required fields are filled
    if (empty($title) || empty($author) || empty($genre)) {
        $_SESSION['error'] = 'Please fill in all fields.';
        header("Location: ../admin/dashboard.php"); 
        exit;
    }

    // Insert the new book into the database
    $insert_query = "INSERT INTO books (title, author, genre, available) VALUES ($1, $2, $3, $4)";
    
    $result = pg_query_params($conn, $insert_query, array($title, $author, $genre, $available));
    
    if ($result) {
        // Log the activity for adding the book (for both Admin and Librarian)
        $user_id = $_SESSION['user_id']; 
        $user_type = $_SESSION['role'] == 1 ? 'Admin' : 'Librarian';  
        $action = 'add';  
        $table_name = 'books';  
        $timestamp = date('Y-m-d H:i:s'); 

        // Insert a log entry into the activity_logs table
        $log_query = "INSERT INTO activity_logs (user_id, user_type, table_name, action, timestamp) 
                      VALUES ($1, $2, $3, $4, $5)";
        
        $log_result = pg_query_params($conn, $log_query, array($user_id, $user_type, $table_name, $action, $timestamp));

        if ($log_result) {
            $_SESSION['success'] = 'Book added and activity logged successfully!';
        } else {
            $_SESSION['error'] = 'Book added, but failed to log activity.';
        }

        // Redirect to the appropriate page based on user role
        if ($_SESSION['role'] == 1) {
            header("Location: ../dashboard/admin.php");
        } elseif ($_SESSION['role'] == 2) {
            header("Location: ../dashboard/librarian.php");
        }
        exit;
    } else {
        $_SESSION['error'] = 'Error adding book. Please try again.';
        header("Location: ../dashboard/admin.php");
        exit;
    }
} else {
    header("Location: ../dashboard/admin.php");
    exit;
}
?>