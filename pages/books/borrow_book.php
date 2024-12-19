<?php
session_start();
include '../../config.php';


if (!isset($conn) || !$conn) {
    die("Database connection not established. Check config.php.");
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: ../pages/login.php");
    exit;
}

$book_id = isset($_POST['book_id']) ? $_POST['book_id'] : null;
$user_id = $_SESSION['user_id'];


if (!$book_id) {
    die("Invalid book ID.");
}

// Check if the book is available
$query = "SELECT available FROM books WHERE id = $1";
$result = pg_query_params($conn, $query, [$book_id]);
if (!$result) {
    die("Error checking book availability: " . pg_last_error($conn));
}

$book = pg_fetch_assoc($result);
if ($book['available'] !== 't') {
    die("Sorry, this book is not available for borrowing.");
}

// Calculate the due date
$due_date = date('Y-m-d', strtotime('+7 days')); // 7 days from now

// Insert a new transaction for borrowing the book with the due date
$transaction_query = "
    INSERT INTO transactions (book_id, user_id, transaction_type, transaction_date, due_date)
    VALUES ($1, $2, 'borrow', NOW(), $3)
";
$transaction_result = pg_query_params($conn, $transaction_query, [$book_id, $user_id, $due_date]);
if (!$transaction_result) {
    die("Error processing the borrowing transaction: " . pg_last_error($conn));
}

// Update the book's availability to false
$update_query = "
    UPDATE books
    SET available = 'f'
    WHERE id = $1
";
$update_result = pg_query_params($conn, $update_query, [$book_id]);
if (!$update_result) {
    die("Error updating book availability: " . pg_last_error($conn));
}


header("Location: ../dashboard/student.php");
exit;
?>
