<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .sidebar {
            border-radius: 10px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background: #D6CDA4;
            padding-top: 20px;
        }

        .sidebar a {
            color: #374151;
            font-weight: bold;
            padding: 20px;
            text-decoration: none;
            display: block;
            margin: 5px 0;
        }

        .sidebar a:hover {
            border-radius: 10px; 
            background: #374151;
            color: white;
        }

        .sidebar a.active {
            font-weight: bolder;
            background: #374151; 
            color: white; 
            border-radius: 10px; 
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2 class="logo text-center mt-5 ms-0" style="font-family:'Courier New', Courier, monospace; font-weight: bolder; font-size: 1.8rem; font-style: normal; color: #374151">
            Archieva
        </h2>
        <hr class="text-white fw-bold">
        <a href="student.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="borrowed_books.php"><i class="fas fa-book-open"></i> My Borrowed Books</a>
        <a href="../actions/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <script>
        // JavaScript to dynamically add the active class
        const links = document.querySelectorAll('.sidebar a'); // Select all links
        const currentPage = window.location.pathname.split("/").pop(); // Get current page name only

        links.forEach(link => {
            const href = link.getAttribute('href');
            if (currentPage === href) {
                link.classList.add('active'); // Add active class
            }
        });
    </script>
</body>
