<?php
session_start();
include '../../config.php';

// Validate the database connection
if (!isset($conn) || !$conn) {
    die("Database connection not established. Check config.php.");
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: ../pages/login.php");
    exit;
}

// Helper function to safely execute queries
function executeQuery($conn, $query, $errorMessage)
{
    $result = pg_query($conn, $query);
    if (!$result) {
        die("$errorMessage: " . pg_last_error($conn));
    }
    return $result;
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
$borrowed_books_result = executeQuery($conn, $borrowed_books_query, "Error fetching borrowed books");

// Query to get returned books
$returned_books_query = "
    SELECT b.id, b.title, b.author, b.genre, t.return_date
    FROM transactions t
    JOIN books b ON t.book_id = b.id
    WHERE t.user_id = $user_id AND t.transaction_type = 'return'
";
$returned_books_result = executeQuery($conn, $returned_books_query, "Error fetching returned books");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <!-- Internal CSS -->
    <style>
        .container-fluid {
            display: flex;
        }

        .content {
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            margin-left: 260px;
            padding: 20px;
            width: calc(100% - 260px);
        }

        .card {
            margin: 10px 0;
            transition: transform 0.3s ease-in-out;
        }

        .card:hover {
            transform: scale(1.05);
        }

        h2 {
            color: #8D9B7F;
        }

        .edit-btn {
            text-decoration: none;
            margin-right: 4px;
            padding: 10px 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .edit-btn:hover {
            background-color: #3e8e41;
        }

        .add-btn {
            font-size: 1rem;
            text-decoration: none;
            padding: 3px 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }

        .add-btn:hover {
            transform: scale(1.05);
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

        .btn:hover {
            background: rgb(14, 125, 20);
            color: white;
        }
    </style>
</head>

<body>
    <?php include '../../templates/librarian_navbar.php'; ?>

    <!-- Content Area -->
    <div class="content mt-3">
        <h1 class="mb-3" style="font-weight: bolder; font-size:2.4rem; letter-spacing: 1px; font-family: Georgia, 'Times New Roman', Times, serif; color: #8D9B7F;">
            <i class="fa fa-book"></i> Manage Books
        </h1>
        <hr>

        <!-- Available Books List -->
        <div class="d-flex align-items-center mt-4 mb-2 ms-2">
            <h2 style="font-size: 1.3rem; font-weight: bold; margin-left: 20px;"><i>Available Books</i></h2>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Availability</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query to get all available books
                $available_books_query = "SELECT id, title, author, genre, available FROM books WHERE available = TRUE";
                $available_books_result = executeQuery($conn, $available_books_query, "Error fetching available books");

                if (pg_num_rows($available_books_result) > 0) {
                    while ($book = pg_fetch_assoc($available_books_result)) {
                        echo "<tr>
                                <td>{$book['id']}</td>
                                <td>{$book['title']}</td>
                                <td>{$book['author']}</td>
                                <td>{$book['genre']}</td>
                                <td class='fw-bold text-success'>Available</td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No available books found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Combined Books Section -->
        <div class="mt-4">
            <h2 style="font-size: 1.3rem; font-weight: bold; margin-left: 20px;"><i>Borrowed and Returned Books</i></h2>
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

    </div>

</html>


<!-- Bootstrap and JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>

</html>