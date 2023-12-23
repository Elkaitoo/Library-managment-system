<?php
session_start(); // Start the session

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("Location: login.php");
    exit;
}

if( $_SESSION["loggedin"] == true && $_SESSION["is_admin"] == 1){
    header("Location: AdminHome.php");
    exit;
}



// Logout user

?>

<!DOCTYPE html>
<html>
<head>
    <title>Online Library</title>
    <link rel="stylesheet" type="text/css" href="./CSS/styles.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <img src="./Images/logo.png" alt="Library Logo">
            </div>
            <button class="hamburger">&#9776;</button>
            <ul class="nav-links">
                <li><a href="logout.php">Logout</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="browseRegistered.php">View Books</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="featured" style="height: 670px;">
        <h1 > Welcome, <br> Mr/Miss with CPR <br> <?php echo htmlspecialchars($_SESSION["username"]); ?>! </h1>
            <p style="width: 570px;" >An online library to <br> Explore a vast collection of books at your fingertips.</p>
            <!-- Additional content can go here -->
        </section>
    </main>

    <footer>
        <p>&copy; 2023 MyLibrary. All rights reserved.</p>
    </footer>

    <script src=".\Javascript\HumburgerMenu.js">  </script>
</body>
</html>
