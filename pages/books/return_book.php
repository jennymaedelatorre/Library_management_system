<?php
session_start();
include '../../config.php';

// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: ../pages/login.php");
    exit;
}


$transaction_id = isset($_POST['transaction_id']) ? $_POST['transaction_id'] : null;
$book_id = isset($_POST['book_id']) ? $_POST['book_id'] : null;
$user_id = $_SESSION['user_id'];


if (!$transaction_id || !$book_id) {
    die("Invalid transaction or book ID.");
}

// Update the transaction to mark the book as returned
$query = "
    UPDATE transactions
    SET transaction_type = 'return', return_date = NOW()
    WHERE id = $1 AND user_id = $2 AND transaction_type = 'borrow'
";
$result = pg_query_params($conn, $query, [$transaction_id, $user_id]);

if (!$result) {
    die("Error processing return transaction: " . pg_last_error($conn));
}

// Update the book availability to true (available)
$update_query = "
    UPDATE books
    SET available = 't'
    WHERE id = $1
";
$update_result = pg_query_params($conn, $update_query, [$book_id]);

if (!$update_result) {
    die("Error updating book availability: " . pg_last_error($conn));
}


header("Location: ../dashboard/student.php");
exit;
?>
