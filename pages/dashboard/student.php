<?php
session_start();
include '../../config.php';

// Validate the database connection
if (!isset($conn) || !$conn) {
    die("Database connection not established. Check config.php.");
}

// Verify user session and role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: ../pages/login.php");
    exit;
}

// Debugging: Ensure session has user_id
if (!isset($_SESSION['user_id'])) {
    die("Error: User ID is not set. Please log in.");
}

// Assign user_id to a variable
$user_id = $_SESSION['user_id'];

// Helper function to safely execute queries
function executeQuery($conn, $query, $params, $errorMessage)
{
    $result = pg_query_params($conn, $query, $params);
    if (!$result) {
        die("$errorMessage: " . pg_last_error($conn));
    }
    return $result;
}

// Search handling
$search_query = ''; // Default to no search condition
$params = [$user_id]; // Initialize query parameters

if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $search_value = '%' . pg_escape_string($_GET['search']) . '%';
    $search_query = "AND (b.title ILIKE $2 OR b.author ILIKE $2 OR b.genre ILIKE $2)";
    $params[] = $search_value; // Add search term as a parameter
}

// Query to fetch books based on search term and user borrow status
$book_query = "
    SELECT 
        b.id, 
        b.title, 
        b.author, 
        b.genre, 
        b.available, 
        CASE 
            WHEN t.user_id = $1 THEN TRUE 
            ELSE FALSE 
        END AS is_borrowed_by_user
    FROM books b
    LEFT JOIN transactions t 
        ON b.id = t.book_id AND t.transaction_type = 'borrow'
    WHERE 1=1 $search_query
";

// Execute query
$book_result = executeQuery($conn, $book_query, $params, "Error fetching books");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">

    <style>
        .container-fluid {
            display: flex;
        }

        .content {
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 260px);
        }

        table {
            width: 98%;
            max-width: 98%;
            margin-left: 20px;
            border-collapse: collapse;
        }

        .content table th {
            background-color: rgb(223, 220, 220);
        }

        .btn {
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

        .disable-btn {
            border-radius: 3px;
            background: rgb(225, 48, 48);
            color: white;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <?php include '../../templates/student_navbar.php'; ?>

    <div class="content mt-5">
        <h1 class="mb-3" style="font-weight: bolder; letter-spacing: 2px; font-family: Georgia, 'Times New Roman', Times, serif; color: #4CAF50;">
            Welcome, <?php echo htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); ?>!
        </h1>
        <hr>

        <!-- List of Books -->
        <h2 class="mt-4" style="font-size: 1.2rem; font-weight: bold; margin-left: 20px; display: flex; justify-content: space-between; align-items: center;">
            <i>Explore Books You Like!</i>

            <!-- Search Form -->
            <form method="GET" action="" class="d-flex ms-3" style="max-width: 500px;">
                <input type="text" name="search" class="form-control" placeholder="Search for books..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                <button type="submit" class="btn btn-primary ms-2">Search</button>
            </form>
        </h2>

        <!-- Books Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (pg_num_rows($book_result) > 0) {
                    while ($book = pg_fetch_assoc($book_result)) {
                        $is_borrowed = $book['is_borrowed_by_user'] === 't';
                        $is_available = $book['available'] === 't';

                        // Set book status
                        if ($is_borrowed) {
                            $status = "<span class='text-secondary fw-bold'>Borrowed</span>";
                        } elseif (!$is_available) {
                            $status = "<span class='text-danger fw-bold'>Not Available</span>";
                        } else {
                            $status = "<span class='text-success'>Available</span>";
                        }

                        echo "<tr>
                        <td>" . htmlspecialchars($book['id']) . "</td>
                        <td>" . htmlspecialchars($book['title']) . "</td>
                        <td>" . htmlspecialchars($book['author']) . "</td>
                        <td>" . htmlspecialchars($book['genre']) . "</td>
                        <td>" . $status . "</td>
                        <td>";

                        if ($is_borrowed || !$is_available) {
                            echo "<button class='disable-btn' disabled>Not Available</button>";
                        } else {
                            echo "<a href='#' 
                                class='btn btn-primary borrow-btn' 
                                data-bs-toggle='modal' 
                                data-bs-target='#borrowModal' 
                                data-id='" . htmlspecialchars($book['id']) . "' 
                                data-title='" . htmlspecialchars($book['title']) . "'>
                                Borrow
                            </a>";
                        }

                        echo "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No books found matching your search.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <!-- Borrow Modal -->
    <div class="modal fade" id="borrowModal" tabindex="-1" aria-labelledby="borrowModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="borrowModalLabel">Borrow Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to borrow this book?</p>
                    <form id="borrowForm" method="POST" action="../books/borrow_book.php">
                        <input type="hidden" id="borrowBookId" name="book_id">
                        <button type="submit" class="btn btn-primary">Confirm Borrow</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


        <!-- Bootstrap and JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.borrow-btn').forEach((button) => {
            button.addEventListener('click', () => {
                const bookId = button.getAttribute('data-id');
                const bookTitle = button.getAttribute('data-title');
                document.querySelector('#borrowBookId').value = bookId;
                document.querySelector('#borrowModalLabel').textContent = "Borrow " + bookTitle;
            });
        });
    });
    </script>

</body>

</html>
