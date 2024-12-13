<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: ../pages/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
</head>
<body>
    <h1>Welcome Student, <?php echo $_SESSION['name']; ?>!</h1>
    <p>This is your dashboard.</p>
</body>
</html>
