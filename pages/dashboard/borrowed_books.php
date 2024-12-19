<?php
session_start();
include '../../config.php';

// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: ../pages/login.php");
    exit;
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowed Books</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">

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
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 260px);
        }
        .btn {
            font-size: 1rem;
            text-decoration: none;
            padding: 5px 15px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }

        .btn:hover {
            transform: scale(1.05);
        }

        .btn-danger {
            background: rgb(213, 53, 44);
            color: white;
        }

        .btn-danger:hover {
            background: rgb(196, 19, 19);
        }

        table {
            width: 98%;
            max-width: 98%;
            margin-left: 20px;
            margin: left;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 25px 30px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .content table th {
            background-color:rgb(223, 220, 220);
        }

        .return-btn {
            background-color: #f44336;
        }

        .return-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>

<body>
    <?php include '../../templates/student_navbar.php'; ?>

    <div class="content mt-3">
        <h1 class="mb-3" style="font-weight: bolder; font-size:2.4rem; letter-spacing: 1px; font-family: Georgia, 'Times New Roman', Times, serif; color: #374151;">
            <i class="fa fa-book"></i> Your Borrowed Books
        </h1>
        <hr>


        <!-- List of Borrowed Books -->
        <table class="table table-bordered" style="margin-top: 30px;">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Borrowed Date</th>
                    <th>Due Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $user_id = $_SESSION['user_id']; 
                $borrowed_query = "
                    SELECT 
                        t.id AS transaction_id, 
                        b.id AS book_id,
                        b.title, 
                        b.author, 
                        b.genre, 
                        t.transaction_date AS borrowed_date, 
                        t.due_date,
                        t.return_date
                    FROM transactions t
                    INNER JOIN books b ON t.book_id = b.id
                    WHERE t.user_id = $1 AND t.transaction_type = 'borrow'
                    ORDER BY t.transaction_date DESC";


                $borrowed_result = pg_query_params($conn, $borrowed_query, array($user_id));

                if (pg_num_rows($borrowed_result) > 0) {
                    while ($borrowed = pg_fetch_assoc($borrowed_result)) {
                        echo "<tr>
                        <td>{$borrowed['transaction_id']}</td>
                        <td>" . htmlspecialchars($borrowed['title']) . "</td>
                        <td>" . htmlspecialchars($borrowed['author']) . "</td>
                        <td>" . htmlspecialchars($borrowed['genre']) . "</td>
                        <td>" . htmlspecialchars(date('F j, Y', strtotime($borrowed['borrowed_date']))) . "</td>
                        <td>" . htmlspecialchars(date('F j, Y', strtotime($borrowed['due_date']))) . "</td>
                        <td>
                            <form action='../books/return_book.php' method='POST' style='display: inline;'>
                                <input type='hidden' name='transaction_id' value='{$borrowed['transaction_id']}'>
                                <input type='hidden' name='book_id' value='{$borrowed['book_id']}'>
                                <button type='submit' class='btn btn-danger return-btn'>Return</button>
                            </form>
                        </td>
                    </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>You have not borrowed any books yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>

</html>