<?php
session_start();
include '../../config.php';

// Check if the user is logged in and has the correct role (Admin or Librarian)
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: ../login.php");
    exit;
}

date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $image = $_FILES['image'];
    $target_dir = "../assets/images/"; // Path to the 'uploads' directory

    // Check if the directory exists, if not, create it
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); 
    }

    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($image['type'], $allowed_types)) {
        die("Only JPG, PNG, or GIF files are allowed.");
    }

    // Check file size (max 5MB)
    if ($image['size'] > 5 * 1024 * 1024) {
        die("File is too large. Maximum file size is 5MB.");
    }

    // Generate a unique file name to avoid conflicts
    $unique_file_name = uniqid('book_', true) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
    $target_file = $target_dir . $unique_file_name; // Final file path

    // Move the uploaded file to the 'uploads' directory
    if (move_uploaded_file($image['tmp_name'], $target_file)) {
        // Save the file path to the database
        $image_path = $unique_file_name; 

        $available = isset($_POST['available']) ? 1 : 0; 

        // Insert the book details along with the image path and availability status into the database
        $query = "INSERT INTO books (title, author, genre, image_path, available) VALUES ($1, $2, $3, $4, $5)";
        $params = [$_POST['title'], $_POST['author'], $_POST['genre'], $image_path, $available];
        $result = pg_query_params($conn, $query, $params);

        if ($result) {
            // Log the success of the book addition
            $user_id = $_SESSION['user_id'];
            $user_type = $_SESSION['role'] == 1 ? 'Admin' : 'Librarian';
            $action = 'ADD'; 
            $table_name = 'books';
            $timestamp = date('Y-m-d H:i:s');

            // Insert the log entry
            $log_query = "INSERT INTO activity_logs (user_id, user_type, table_name, action, timestamp) 
                          VALUES ($1, $2, $3, $4, $5)";
            $log_result = pg_query_params($conn, $log_query, array($user_id, $user_type, $table_name, $action, $timestamp));

            if ($log_result) {
                // Redirect based on user role (Admin or Librarian)
                $_SESSION['success'] = 'The book has been added successfully and the activity was logged.';
                if ($_SESSION['role'] == 1) {
                    header("Location: ../dashboard/admin.php");
                } else {
                    header("Location: ../dashboard/librarian.php");
                }
                exit; 
            } else {
                $_SESSION['error'] = 'The book was added successfully, but there was an error logging the activity.';
                header("Location: ../dashboard/admin.php");
                exit;
            }
        } else {
            $_SESSION['error'] = 'Error adding book to the database.';
            header("Location: ../dashboard/admin.php");
            exit; 
        }
    } else {
        $_SESSION['error'] = 'Sorry, there was an error uploading your file.';
        header("Location: ../dashboard/admin.php");
        exit; 
    }
} else {
    header("Location: ../dashboard/admin.php");
    exit; 
}
?>
