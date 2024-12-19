<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<div class="sidebar" style="height: 100vh; position: fixed; top: 0; left: 0; width: 250px; background: #8D9B7F; padding-top: 20px;">
    <style>
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background: #8D9B7F;
            padding-top: 20px;
        }

        .logo h2 {
            font-family: 'Courier New', Courier, monospace;
        }

        .sidebar a {
            color: #ffffff;
            font-weight: bold;
            padding: 20px;
            text-decoration: none;
            display: block;
            margin: 5px 0;
            border-radius: 10px;
        }

        .sidebar a:hover {
            background: #D1B26F;
            color: white;
        }

        .sidebar a.active {
            background: #D1B26F;
            color: #2D3748;
            font-weight: bolder;
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
    
    <h2 class="logo text-center mt-5 ms-0" style="font-family:Georgia, 'Times New Roman', Times, serif; font-weight:bolder; font-size:1.8rem; font-style:normal;">Archieva</h2>
    <hr class="text-white fw-bold">
    <a href="../dashboard/librarian.php" class="sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="../dashboard/student_borrow.php" class="sidebar-link"><i class="fas fa-book"></i> Manage Books</a>
    <a href="../actions/logout.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all sidebar links
        const links = document.querySelectorAll('.sidebar a');
        
        // Get the current page URL (path)
        const currentPage = window.location.pathname;
        console.log('Current page:', currentPage); // Debugging line to check current page
        
        // Loop through all the links and add the 'active' class if the link's href matches the current page URL
        links.forEach(link => {
            const linkHref = link.getAttribute('href');
            console.log('Checking link:', linkHref); // Debugging line to check each link's href
            
            // Remove relative part (e.g. ../) from the link href if it exists
            const absoluteLinkHref = linkHref.startsWith('../') ? linkHref.replace('../', '') : linkHref;

            console.log('Comparing with:', absoluteLinkHref); // Debugging line to check the transformed link href
            
            // Exact match with the currentPage
            if (currentPage.endsWith(absoluteLinkHref)) {
                link.classList.add('active');
                console.log('Active link:', absoluteLinkHref); // Debugging line to check which link is activated
            }
        });
    });
</script>
