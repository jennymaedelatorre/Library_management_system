<?php
session_start();
include '../../config.php';


if (!isset($conn) || !$conn) {
    die("Database connection not established. Check config.php.");
}

// Check if the user is logged in and has the correct role (Admin or Librarian)
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: ../login.php");
    exit;
}


function executeQuery($conn, $query, $errorMessage)
{
    $result = pg_query($conn, $query);
    if (!$result) {
        die("$errorMessage: " . pg_last_error($conn));
    }
    return $result;
}

date_default_timezone_set('Asia/Manila');


if (isset($_POST['id'])) {
    $book_id = $_POST['id'];

   
    pg_query($conn, "BEGIN");

    $delete_query = "DELETE FROM books WHERE id = $1";
    $delete_result = pg_query_params($conn, $delete_query, [$book_id]);

    if ($delete_result) {
        // Log the activity: Book deletion
        $user_id = $_SESSION['user_id'];  
        $user_type = $_SESSION['role'] == 1 ? 'Admin' : 'Librarian';  
        $action = 'delete';  
        $table_name = 'books';  
        $timestamp = date('Y-m-d H:i:s'); 

        // Insert a log entry into the activity_logs table
        $log_query = "INSERT INTO activity_logs (user_id, user_type, table_name, action, timestamp) 
                      VALUES ($1, $2, $3, $4, $5)";
        $log_result = pg_query_params($conn, $log_query, array($user_id, $user_type, $table_name, $action, $timestamp));

        if ($log_result) {
            pg_query($conn, "COMMIT");
            
            // Redirect based on user role (Admin or Librarian)
            if ($_SESSION['role'] == 1) {
                header("Location: ../dashboard/admin.php?success=Book+deleted+and+activity+logged");
            } elseif ($_SESSION['role'] == 2) {
                header("Location: ../dashboard/librarian.php?success=Book+deleted+and+activity+logged");
            }
            exit;
        } else {
            pg_query($conn, "ROLLBACK");
            echo "Error logging activity for book deletion.";
            exit;
        }
    } else {
        pg_query($conn, "ROLLBACK");
        echo "Error deleting book!";
        exit;
    }
} else {
    header("Location: ../pages/books/admin_books.php?error=No+book+ID+provided");
    exit;
}
?>
