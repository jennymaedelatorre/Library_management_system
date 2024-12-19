<?php
session_start();
include '../../config.php'; // Database connection

// Ensure the user ID is passed
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Make sure the ID is valid (you can also do additional validation here)
    if (is_numeric($user_id)) {
        // Perform the deletion query
        $delete_query = "DELETE FROM users WHERE id = $1";
        $result = pg_query_params($conn, $delete_query, array($user_id));

        if ($result) {
            $_SESSION['success'] = 'User deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete user: ' . pg_last_error($conn);
        }
    } else {
        $_SESSION['error'] = 'Invalid user ID provided.';
    }
} else {
    $_SESSION['error'] = 'No user ID provided.';
}

// Redirect back to the manage user page with a session message
header("Location: ../dashboard/manage_user.php");
exit();
?>
