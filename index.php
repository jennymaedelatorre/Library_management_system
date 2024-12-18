<?php include 'templates/header.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>

    <!-- Internal CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-image: url('assets/images/b5.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        .blur-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(1px);
            z-index: -1;
        }

        h1 {
            font-size: 40px;
            letter-spacing: 1px;
            color: #fff;
            margin-bottom: 20px;
        }

        .intro {
            font-size: 1.2rem;
            margin-bottom: 30px;
            max-width: 900px;
            line-height: 1.6;
        }

        p {
            font-size: 18px;
            margin-top: 20px;
        }

        a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }


        footer {
            position: absolute;
            bottom: 0px;
            width: 100%;
            text-align: center;
            font-size: 12px;
            color: #fff;
            background: rgba(0, 0, 0, 0.5);
            padding: 20px 0;
        }
    </style>
</head>

<body>
    <div class="blur-overlay"></div>

    <div>
        <h1>Welcome to Archiva!</h1>
        <p class="intro">
            Archiva is your gateway to a world of endless stories, knowledge, and learning.
            Whether you're a student, librarian, or administrator, our system is designed to
            make managing and discovering books easier than ever. Dive into a seamlessly organized
            library experience tailored just for you.
        </p>
        <p><a href="pages/login.php">Login</a> or <a href="pages/register.php">Register</a> to get started.</p>
    </div>

    <?php include 'templates/footer.php'; ?>
</body>