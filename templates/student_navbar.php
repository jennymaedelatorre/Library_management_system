
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<div class="sidebar" style="height: 100vh; position: fixed; top: 0; left: 0; width: 250px; background: #4CAF50; padding-top: 20px;">
    <style>
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background: #4CAF50;
            padding-top: 20px;
        }
        .logo h2{
            font-family: 'Courier New', Courier, monospace;
        }
        .sidebar a {
            color: white;
            padding: 10px;
            text-decoration: none;
            display: block;
            margin: 5px 0;
        }

        .sidebar a:hover {
            background: #45a049;
            color: white;
        }

        .sidebar h2 {
            color: white;
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar i {
            margin-right: 10px; 
        }
    </style>
    <h2 class="logo text-center mt-5 ms-0" style="font-family:Georgia, 'Times New Roman', Times, serif; font-weight:bolder; font-size:1.8rem; font-style:normal;">StoryScape</h2>
    <hr class="text-white fw-bold">
    <a href="../dashboard/student.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="../dashboard/borrowed_books.php"><i class="fas fa-book-open"></i> My Borrowed Books</a> 
    <a href="../actions/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>

</div>
