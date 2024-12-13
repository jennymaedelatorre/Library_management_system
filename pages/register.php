<?php include '../templates/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - StoryScape</title>

    <!-- Internal CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Page background */
        body {
            background-image: url('../assets/images/b5.jpg'); /* Path to your image */
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
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black overlay */
            backdrop-filter: blur(1px);
            z-index: -1;
        }

        /* Form Container */
        .register-container {
            background: rgba(0, 0, 0, 0.7); /* Semi-transparent background */
            padding: 30px;
            border-radius: 10px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
            margin-top: 80px;
            margin-bottom: 80px;
        }

        h1 {
            margin-top: 10px;
            font-size: 1.6rem;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }

        form {
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size:0.8rem;
            text-align: left;
            font-weight: bold;
            
        }

        input, select {
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
            transition: transform 0.2s ease-in-out;
            margin-top: 10px;
            margin-bottom:10px;
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

    <div class="register-container">
        <h1>Register</h1>
        <form action="../actions/register_action.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" name="name" required>
            
            <label for="email">Email:</label>
            <input type="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            
            <label for="role">Role:</label>
            <select name="role" required>
                <option value="1">Admin</option>
                <option value="2">Librarian</option>
                <option value="3">Student</option>
            </select>
            
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>

   
</body>
</html>
