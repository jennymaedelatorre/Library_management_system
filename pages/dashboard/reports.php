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
            color: #64748B;
        }

        .content {
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            margin-left: 240px;
            padding-left: 20px;
            width: calc(100% - 240px);
            background-color: #F3F4F6;
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

        .logs-table th,
        .borrow-return th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include '../../templates/navbar.php'; ?>

    <div class="container-fluid">
        <div class="content" style="padding-top: 30px;">
            <h1 class="mb-3" style="font-weight: bolder; letter-spacing:px; font-family: Georgia, 'Times New Roman', Times, serif; color: #64748B;"">
            <i class=" fas fa-chart-bar"></i> Activity Logs Report
            </h1>
            <hr>

            <!-- Combined Books Section -->
            <div class="mt-4">
                <h2><i>Borrowed and Returned Books</i></h2>
                <table class="table table-bordered borrow-return">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Book ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Due Date</th>
                            <th>Return Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $combined_books_query = "
                                SELECT 
                                    studentid, 
                                    studentname,  -- Added Student Name
                                    bookid, 
                                    title, 
                                    author, 
                                    duedate, 
                                    NULL AS returndate, 
                                    'Borrowed' AS status,
                                    duedate AS sort_date
                                FROM GetCurrentlyBorrowedBooks()
                            
                                UNION ALL
                            
                                SELECT 
                                    studentid, 
                                    studentname,  -- Added Student Name
                                    bookid, 
                                    title, 
                                    author, 
                                    NULL AS duedate, 
                                    returndate, 
                                    'Returned' AS status,
                                    returndate AS sort_date
                                FROM GetReturnedBooks()
                            
                                ORDER BY sort_date DESC NULLS LAST
                                ";

                        // Execute the query
                        $combined_books_result = pg_query($conn, $combined_books_query);

                        if (!$combined_books_result) {
                            die("Error fetching books: " . pg_last_error($conn));
                        }

                        // Display the books
                        if (pg_num_rows($combined_books_result) > 0) {
                            while ($book = pg_fetch_assoc($combined_books_result)) {
                                // Display the respective dates based on status
                                $due_date = $book['duedate'] ? $book['duedate'] : '-';
                                $return_date = $book['returndate'] ? $book['returndate'] : '-';

                                echo "<tr>
                                        <td>{$book['studentid']}</td>
                                        <td>{$book['studentname']}</td> <!-- Display Student Name -->
                                        <td>{$book['bookid']}</td>
                                        <td>{$book['title']}</td>
                                        <td>{$book['author']}</td>
                                        <td>{$due_date}</td>
                                        <td>{$return_date}</td>
                                        <td>{$book['status']}</td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No borrowed or returned books found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>



            <!-- Combined Activity Logs Section -->
            <h2 class="mt-5">List of Activity Logs</h2>
            <table class="table table-bordered logs-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">Log ID</th>
                        <th style="width: 15%;">Timestamp</th>
                        <th style="width: 15%;">User ID</th>
                        <th style="width: 10%;">User Type</th>
                        <th style="width: 15%;">Table Name</th>
                        <th style="width: 10%;">Action</th>
                        <th style="width: 15%;">Log Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $combined_log_query = "
                        SELECT id, timestamp, user_id, user_type, table_name, action, 'User Log' AS log_type 
                        FROM activity_logs 
                        WHERE table_name = 'users'
                        
                        UNION ALL
                        
                        SELECT id, timestamp, user_id, user_type, table_name, action, 'Book Log' AS log_type 
                        FROM activity_logs 
                        WHERE table_name = 'books'
                        
                        ORDER BY timestamp DESC
                        LIMIT 10
                    ";
                    $combined_log_result = pg_query($conn, $combined_log_query);

                    if ($combined_log_result && pg_num_rows($combined_log_result) > 0) {
                        while ($log = pg_fetch_assoc($combined_log_result)) {
                            echo "<tr>
                    <td>{$log['id']}</td>
                    <td>{$log['timestamp']}</td>
                    <td>{$log['user_id']}</td>
                    <td>{$log['user_type']}</td>
                    <td>{$log['table_name']}</td>
                    <td>{$log['action']}</td>
                    <td>{$log['log_type']}</td>
                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No activity logs found.</td></tr>";
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