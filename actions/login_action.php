<?php
include '../config.php';

// Retrieve input data
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$password = $_POST['password'];

// Validate email
if (!$email) {
    echo "Invalid email. Please try again.";
    exit;
}

// Prepare the query to check user credentials
$query = "SELECT id, name, password, role FROM users WHERE email = $1";
$result = pg_query_params($conn, $query, [$email]);

if ($result && pg_num_rows($result) === 1) {
    $user = pg_fetch_assoc($result);

    // Verify the password
    if (password_verify($password, $user['password'])) {
        // Start the session and store user data
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        switch ($user['role']) {
            case 1: // Admin role
                header("Location: ../pages/dashboard/admin.php");
                break;
            case 2: // Librarian role
                header("Location: ../pages/dashboard/librarian.php");
                break;
            case 3: // Student role
                header("Location: ../pages/dashboard/student.php");
                break;
            default:
                echo "Invalid role. Contact system administrator.";
                exit;
        }
    } else {
        echo "Incorrect password.";
    }
} else {
    echo "User not found. Please check your credentials.";
}

// Close the database connection
pg_close($conn);
?>
