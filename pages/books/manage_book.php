<?php
session_start();
include '../../config.php';

// Validate the database connection
if (!isset($conn) || !$conn) {
    die("Database connection not established. Check config.php.");
}

// Check if the user is logged in and has the correct role (Admin)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../login.php");
    exit;
}

$is_edit = false;
$book = null;

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];
    $book_query = "SELECT * FROM books WHERE id = $book_id";
    $book_result = pg_query($conn, $book_query);
    if (!$book_result) {
        die("Error fetching book: " . pg_last_error($conn));
    }
    
    $book = pg_fetch_assoc($book_result);
    if (!$book) {
        die("Book not found.");
    }
    $is_edit = true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $available = isset($_POST['available']) ? 1 : 0;

    if ($is_edit) {
        // Update existing book
        $update_query = "UPDATE books SET title = '$title', author = '$author', genre = '$genre', available = $available WHERE id = {$book['id']}";
        $update_result = pg_query($conn, $update_query);
        if (!$update_result) {
            die("Error updating book: " . pg_last_error($conn));
        }
        header("Location: admin_dashboard.php"); 
        exit;
    } else {
        // Add new book
        $insert_query = "INSERT INTO books (title, author, genre, available) VALUES ('$title', '$author', '$genre', $available)";
        $insert_result = pg_query($conn, $insert_query);
        if (!$insert_result) {
            die("Error inserting book: " . pg_last_error($conn));
        }
        header("Location: admin_dashboard.php"); 
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? "Edit Book" : "Add Book"; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../../templates/navbar.php'; ?>

    <div class="container mt-5">
        <h2><?php echo $is_edit ? "Edit Book: {$book['title']}" : "Add New Book"; ?></h2>

        <!-- Form for Adding/Editing Book -->
        <form method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo $is_edit ? $book['title'] : ''; ?>" required>
            </div>

            <div class="mb-3">
                <label for="author" class="form-label">Author</label>
                <input type="text" class="form-control" id="author" name="author" value="<?php echo $is_edit ? $book['author'] : ''; ?>" required>
            </div>

            <div class="mb-3">
                <label for="genre" class="form-label">Genre</label>
                <input type="text" class="form-control" id="genre" name="genre" value="<?php echo $is_edit ? $book['genre'] : ''; ?>" required>
            </div>

            <div class="mb-3">
                <label for="available" class="form-label">Availability</label>
                <input type="checkbox" id="available" name="available" <?php echo $is_edit && $book['available'] ? 'checked' : ''; ?>>
                <label for="available">Available</label>
            </div>

            <button type="submit" class="btn btn-primary"><?php echo $is_edit ? "Update Book" : "Add Book"; ?></button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
