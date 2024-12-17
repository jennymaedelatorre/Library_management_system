<?php
include '../config.php';

// Validate and sanitize user input
$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
$role = filter_var($_POST['role'], FILTER_VALIDATE_INT);

// Check if all fields are valid
if (!$email || !$role) {
    echo "Invalid input. Please check your email and role.";
    exit;
}


$query = "INSERT INTO users (name, email, password, role) VALUES ($1, $2, $3, $4)";


$result = pg_query_params($conn, $query, [$name, $email, $password, $role]);

// Check the result
if ($result) {
    // Registration successful - Notify and redirect to login page
    echo "<script>
        alert('Registration successful! Please log in.');
        window.location.href = '../pages/login.php';
    </script>";
} else {
    echo "Error: " . pg_last_error($conn);
}


pg_close($conn);
?>
