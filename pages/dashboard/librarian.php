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


function executeQuery($conn, $query, $errorMessage)
{
    $result = pg_query($conn, $query);
    if (!$result) {
        die("$errorMessage: " . pg_last_error($conn));
    }
    return $result;
}


$students_query = "SELECT COUNT(*) AS total_students FROM users WHERE role = 3";
$students_result = pg_query($conn, $students_query);
$total_students = pg_fetch_result($students_result, 0, 'total_students');

$borrowed_books_stats_query = "SELECT total_borrowed_books, total_books_notavailable, total_books_available, total_books FROM borrowed_books_stats";
$borrowed_books_stats_result = executeQuery($conn, $borrowed_books_stats_query, "Error fetching borrowed books stats");

// Initialize default values
$total_books_borrowed = 0;
$total_books_notavailable = 0;
$total_books_available = 0;
$total_books = 0;

// Check if the query returns any result for borrowed books stats
if ($borrowed_books_stats_result && pg_num_rows($borrowed_books_stats_result) > 0) {
    // Fetch the stats from the result
    $stats = pg_fetch_assoc($borrowed_books_stats_result);
    
    // Assign values from the result or default to 0 if not available
    $total_books_borrowed = isset($stats['total_borrowed_books']) ? (int)$stats['total_borrowed_books'] : 0;
    $total_books_notavailable = isset($stats['total_books_notavailable']) ? (int)$stats['total_books_notavailable'] : 0;
    $total_books_available = isset($stats['total_books_available']) ? (int)$stats['total_books_available'] : 0;
    $total_books = isset($stats['total_books']) ? (int)$stats['total_books'] : 0;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">

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
        <h1 class="mb-3" style="font-weight: bolder; letter-spacing:px; font-family: Georgia, 'Times New Roman', Times, serif; color: #4CAF50;">
            Welcome, <?php echo htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); ?>!
        </h1>
        <hr>
        <!-- Statistic Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Students</h5>
                        <p class="card-text h3"><?php echo $total_students; ?></p>
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-secondary">
                    <div class="card-body">
                        <h5 class="card-title">Total Books</h5>
                        <p class="card-text h3"><?php echo $total_books; ?></p>
                        <i class="fas fa-book"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Available Books</h5>
                        <p class="card-text h3"><?php echo $total_books_available; ?></p>
                        <i class="fas fa-book-open"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title">Not Available Books</h5>
                        <p class="card-text h3"><?php echo $total_books_notavailable; ?></p>
                        <i class="fas fa-book-dead"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Borrowed Books</h5>
                        <p class="card-text h3"><?php echo $total_books_borrowed; ?></p>
                        <i class="fas fa-bookmark"></i>
                    </div>
                </div>
            </div>
        </div>


        <!-- Book Management -->
        <div class="d-flex align-items-center mt-4 mb-2 ms-2">
            <h2 style="font-size: 1.5rem; font-weight: bold; margin-right: 20px;"><i>List of all books</i></h2>
            <button class="add-btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">Add New Book</button>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Availability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $book_query = "SELECT id, title, author, genre, available FROM books";
                $book_result = executeQuery($conn, $book_query, "Error fetching books");

                if (pg_num_rows($book_result) > 0) {
                    while ($book = pg_fetch_assoc($book_result)) {
                        // Determine availability text
                        if ($book['available'] === 't') {
                            $availability = 'Available';
                            $availability_class = 'fw-bold text-success';
                        } else {
                            // Check if book is borrowed
                            $borrowed_check_query = "SELECT COUNT(*) FROM transactions WHERE book_id = {$book['id']} AND transaction_type = 'borrow'";
                            $borrowed_check_result = executeQuery($conn, $borrowed_check_query, "Error checking borrowed books");
                            $borrowed_count = pg_fetch_result($borrowed_check_result, 0, 0);

                            if ($borrowed_count > 0) {
                                $availability = 'Borrowed';
                                $availability_class = 'fw-bold text-secondary';
                            } else {
                                $availability = 'Not Available';
                                $availability_class = 'fw-bold text-danger';
                            }
                        }

                        echo "<tr>
                            <td>{$book['id']}</td>
                            <td>{$book['title']}</td>
                            <td>{$book['author']}</td>
                            <td>{$book['genre']}</td>
                            <td class='{$availability_class}'>{$availability}</td>
                            <td>
                                <!-- Edit Button -->
                                <a href='#' 
                                   class='edit-btn btn-warning' 
                                   data-bs-toggle='modal' 
                                   data-bs-target='#editBookModal' 
                                   data-id='{$book['id']}' 
                                   data-title='{$book['title']}' 
                                   data-author='{$book['author']}' 
                                   data-genre='{$book['genre']}' 
                                   data-available='" . ($book['available'] === 't' ? 'true' : 'false') . "'>
                                   Edit
                                </a>
                                
                                <!-- Delete Button -->
                                <a href='#' 
                                   class='delete-btn btn-danger' 
                                   data-bs-toggle='modal' 
                                   data-bs-target='#deleteModal' 
                                   data-id='{$book['id']}' 
                                   data-title='{$book['title']}'>
                                   Delete
                                </a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No books found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for Editing a Book -->
    <div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBookModalLabel">Edit Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editBookForm" method="POST" action="../books/edit.php">
                        <input type="hidden" id="editBookId" name="id"> <!-- Hidden input for Book ID -->
                        <div class="mb-3">
                            <label for="editTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="editTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAuthor" class="form-label">Author</label>
                            <input type="text" class="form-control" id="editAuthor" name="author" required>
                        </div>
                        <div class="mb-3">
                            <label for="editGenre" class="form-label">Genre</label>
                            <input type="text" class="form-control" id="editGenre" name="genre" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="editAvailable" name="available">
                            <label class="form-check-label" for="editAvailable">Available</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal for Adding a New Book -->
    <div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBookModalLabel">Add New Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="../books/add_book.php">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="author" class="form-label">Author</label>
                            <input type="text" class="form-control" id="author" name="author" required>
                        </div>
                        <div class="mb-3">
                            <label for="genre" class="form-label">Genre</label>
                            <input type="text" class="form-control" id="genre" name="genre" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="available" name="available">
                            <label class="form-check-label" for="available">Available</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Book</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this book?</p>
                    <form id="deleteForm" method="POST" action="../books/delete_books.php">
                        <input type="hidden" id="deleteBookId" name="id">
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
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
            document.querySelectorAll('.delete-btn').forEach((button) => {
                button.addEventListener('click', () => {
                    const bookId = button.getAttribute('data-id');
                    const bookTitle = button.getAttribute('data-title');
                    document.querySelector('#deleteBookId').value = bookId;
                    document.querySelector('#deleteBookTitle').textContent = bookTitle;
                });
            });

            document.querySelectorAll('.edit-btn').forEach((button) => {
                button.addEventListener('click', () => {
                    const bookId = button.dataset.id;
                    const bookTitle = button.dataset.title;
                    const bookAuthor = button.dataset.author;
                    const bookGenre = button.dataset.genre;
                    const bookAvailable = button.dataset.available === 'true';

                    // Populate the modal fields
                    document.getElementById('editBookId').value = bookId;
                    document.getElementById('editTitle').value = bookTitle;
                    document.getElementById('editAuthor').value = bookAuthor;
                    document.getElementById('editGenre').value = bookGenre;
                    document.getElementById('editAvailable').checked = bookAvailable;

                    // Show the modal
                    const editModal = new bootstrap.Modal(document.getElementById('editBookModal'));
                    editModal.show();
                });
            });
        });
    </script>

</body>

</html>