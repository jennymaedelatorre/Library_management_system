<?php include '../templates/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StoryScape</title>
    <link rel="stylesheet" href="assets/css/styles.css">

    <!-- Internal CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

       
        body {
            background-image: url('../assets/images/b5.jpg'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            color: #fff;
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

        .login-container {
            background: rgba(0, 0, 0, 0.7); 
            padding: 30px;
            border-radius: 10px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
        }

        h1 {
            font-size: 1.5rem;
            margin-top: 10px;
            letter-spacing: 1px;
        }

        form {
            margin-top: 40px;
            display: flex;
            flex-direction: column;
        }

        label {
            font-size:0.8rem;
            text-align: left;
            font-weight: bold;
            margin-bottom: 5px;
            margin-top: 15px;
        }

        input {
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-bottom: 10px;
            font-size: 16px;
            width: 100%;
        }

        button {
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
            margin-top: 30px;
            margin-bottom:30px;
            transition: transform 0.2s ease-in-out;
        }

        button:hover {
            transform: scale(1.05);
        }
        p{
            font-size: 0.8rem;
        }

        a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
         
    
    </style>
</head>
<body>
    <div class="blur-overlay"></div>

    <div class="login-container">
        <h1>Login to Archieva</h1>
        <form action="../actions/login_action.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required>
            
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
            
            <button type="submit">Login</button>
        </form>
        <p>
            Don't have an account? <a href="register.php">Register here</a>.
        </p>
    </div>

    
</body>
</html>
