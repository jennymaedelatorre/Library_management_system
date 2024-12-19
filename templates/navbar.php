<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<div class="sidebar" style="height: 100vh; position: fixed; top: 0; left: 0; width: 250px; background: #64748B; padding-top: 20px;">
    <style>
        .sidebar {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background: #64748B;
            padding-top: 20px;
        }
        .logo h2 {
            font-family: 'Courier New', Courier, monospace;
        }
        .sidebar a {
            font-weight: bold;
            color: #ffffff;
            padding-left: 20px;
            padding-top: 20px;
            padding-right: 20px;
            padding-bottom: 20px;
            text-decoration: none;
            display: block;
            margin: 5px 0;
        }

        .sidebar a:hover {
            color: #fff;
            border-radius: 10px;
            background: rgba(191, 191, 191, 0.67);
        }

        .sidebar a.active {
            font-weight: bolder;
            border-radius: 10px;
            background:rgba(191, 191, 191, 0.67);
            color: #1F2937;
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
    <h2 class="logo text-center mt-5 ms-0" style="font-family:'Courier New', Courier, monospace; font-weight:bolder; font-size:1.8rem; font-style:normal;">Archieva</h2>
    <hr class="text-white fw-bold">
    <a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="manage_user.php"><i class="fas fa-users"></i> Manage Users</a>
    <a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>
    <a href="../actions/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<script>
    // Get all sidebar links
    const links = document.querySelectorAll('.sidebar a');

    // Get the current page URL
    const currentPage = window.location.href;

    // Loop through all the links and add the 'active' class if the link's href matches the current page URL
    links.forEach(link => {
        if (currentPage.includes(link.getAttribute('href'))) {
            link.classList.add('active');
        }
    });
</script>
