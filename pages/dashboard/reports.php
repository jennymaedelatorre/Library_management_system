<?php
session_start();
include '../../config.php';  

// Check if the user is logged in and has the correct role (Admin)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../login.php");
    exit;
}
// Get user ID
$user_id = $_SESSION['user_id'];

// Query to get borrowed books
$borrowed_books_query = "
    SELECT b.id, b.title, b.author, b.genre, t.due_date
    FROM transactions t
    JOIN books b ON t.book_id = b.id
    WHERE t.user_id = $user_id AND t.transaction_type = 'borrow' AND t.return_date IS NULL
";


$borrowed_books_result = pg_query($conn, $borrowed_books_query);
if (!$borrowed_books_result) {
    die("Error fetching borrowed books: " . pg_last_error($conn));
}


$returned_books_query = "
    SELECT b.id, b.title, b.author, b.genre, t.return_date
    FROM transactions t
    JOIN books b ON t.book_id = b.id
    WHERE t.user_id = $user_id AND t.transaction_type = 'return'
";


$returned_books_result = pg_query($conn, $returned_books_query);
if (!$returned_books_result) {
    die("Error fetching returned books: " . pg_last_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

   
    <style>
       
        .container-fluid {
            display: flex;
        }

        h2 {
            font-family: Georgia, 'Times New Roman', Times, serif;
            font-size: 1rem;
            font-weight: bold;
            margin-left: 20px;
            font-style: italic;
        }

        .content {
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            margin-left: 260px;
            padding: 20px;
            width: calc(100% - 260px);
        }

        .btn {
            padding: 8px 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .btn:hover {
            background: rgb(14, 125, 20);
            color: white;
        }

        .delete-btn {
            padding: 10px 12px;
            background: rgb(213, 53, 44);
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
        }

        .delete-btn:hover {
            background: rgb(196, 19, 19);
        }

        .logs-table th,
        .logs-table td {
            text-align: center;
            vertical-align: middle;
        }

        .logs-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include '../../templates/navbar.php'; ?>

    <div class="container-fluid">
        <div class="content mt-3">
            <h1 class="mb-3" style="font-weight: bolder; font-family: Georgia, 'Times New Roman', Times, serif; color:#4CAF50;">
                Activity Logs Report
            </h1>
            <hr>

            <!-- Borrowed Books Section -->
            <div class="mt-4">
                <h2><i>Borrowed Books</i></h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Student ID</th> <!-- Added Student ID column -->
                            <th>Member ID</th>
                            <th>Book ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Call the GetCurrentlyBorrowedBooks function to get all borrowed books
                        $borrowed_books_query = "
                            SELECT * FROM GetCurrentlyBorrowedBooks()
                        ";
                        $borrowed_books_result = pg_query($conn, $borrowed_books_query);

                        if (!$borrowed_books_result) {
                            die("Error fetching borrowed books: " . pg_last_error($conn));
                        }

                        // Check if there are borrowed books to display
                        if (pg_num_rows($borrowed_books_result) > 0) {
                            while ($book = pg_fetch_assoc($borrowed_books_result)) {
                                echo "<tr>
                                    <td>{$book['studentid']}</td> <!-- Display Student ID -->
                                    <td>{$book['memberid']}</td> <!-- Display Member ID (user ID) -->
                                    <td>{$book['bookid']}</td>
                                    <td>{$book['title']}</td>
                                    <td>{$book['author']}</td>
                                    <td>{$book['duedate']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No borrowed books found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>



            <!-- Returned Books Section -->
            <div class="mt-4">
                <h2><i>Returned Books</i></h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Book ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Return Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Call the GetReturnedBooks function to get all returned books
                        $returned_books_query = "
                            SELECT * FROM GetReturnedBooks()  -- Using the function to fetch returned books
                        ";
                        $returned_books_result = pg_query($conn, $returned_books_query);

                        if (!$returned_books_result) {
                            die("Error fetching returned books: " . pg_last_error($conn));
                        }

                        // Check if there are returned books to display
                        if (pg_num_rows($returned_books_result) > 0) {
                            while ($book = pg_fetch_assoc($returned_books_result)) {
                                echo "<tr>
                                    <td>{$book['studentid']}</td> <!-- Display Student ID -->
                                    <td>{$book['bookid']}</td>
                                    <td>{$book['title']}</td>
                                    <td>{$book['author']}</td>
                                    <td>{$book['returndate']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No returned books found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>


            <!-- Users Activity Logs Section -->
            <h2 class="mt-5">List of User Activity Logs</h2>
            <table class="table table-bordered logs-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">Log ID</th>
                        <th style="width: 15%;">Timestamp</th>
                        <th style="width: 15%;">User ID</th>
                        <th style="width: 10%;">User Type</th>
                        <th style="width: 15%;">Table Name</th>
                        <th style="width: 10%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $user_log_query = "SELECT id, timestamp, user_id, user_type, table_name, action
                    FROM activity_logs
                    WHERE table_name = 'transactions'
                    ORDER BY timestamp DESC
                    LIMIT 6";                
    
                    $user_log_result = pg_query($conn, $user_log_query);

                    if ($user_log_result && pg_num_rows($user_log_result) > 0) {
                        while ($log = pg_fetch_assoc($user_log_result)) {
                            echo "<tr>
                        <td>{$log['id']}</td>
                        <td>{$log['timestamp']}</td>
                        <td>{$log['user_id']}</td>
                        <td>{$log['user_type']}</td>
                        <td>{$log['table_name']}</td>
                        <td>{$log['action']}</td>
                    </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No user activity logs found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Books Activity Logs Section -->
            <h2 class="mt-5">List of Book Activity Logs</h2>
            <table class="table table-bordered logs-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">Log ID</th>
                        <th style="width: 15%;">Timestamp</th>
                        <th style="width: 15%;">User ID</th>
                        <th style="width: 10%;">User Type</th>
                        <th style="width: 15%;">Table Name</th>
                        <th style="width: 10%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $book_log_query = "SELECT id, timestamp, user_id, user_type, table_name, action 
                               FROM activity_logs 
                               WHERE table_name = 'books' 
                               ORDER BY timestamp DESC
                               LIMIT 6";  
                    $book_log_result = pg_query($conn, $book_log_query);

                    if ($book_log_result && pg_num_rows($book_log_result) > 0) {
                        while ($log = pg_fetch_assoc($book_log_result)) {
                            echo "<tr>
                        <td>{$log['id']}</td>
                        <td>{$log['timestamp']}</td>
                        <td>{$log['user_id']}</td>
                        <td>{$log['user_type']}</td>
                        <td>{$log['table_name']}</td>
                        <td>{$log['action']}</td>
                    </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No book activity logs found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS & Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>

</html>
