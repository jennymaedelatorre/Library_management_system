<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: ../pages/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Librarian Dashboard</title>
</head>
<body>
    <h1>Welcome Librarian, <?php echo $_SESSION['name']; ?>!</h1>
    <p>This is your dashboard.</p>
</body>
</html>
