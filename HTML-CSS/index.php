
<?php 

session_start(); // Start the session
if( $_SESSION["loggedin"] == true){
    header("Location: user.php");
    exit;
}
if( $_SESSION["loggedin"] == true &&$_SESSION["is_admin"] == 1){
    header("Location: AdminHome.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Online Library</title>
    <link rel="stylesheet" type="text/css" href="./CSS/styles.css">
</head>
<body>3
    <header>
        <nav>
            <div class="logo">
                <img src="./Images/logo.png" alt="Library Logo">
            </div>
            <button class="hamburger">&#9776;</button>
            <ul class="nav-links">
                <li><a href="login.php">Login</a></li>
                <li><a href="registration.php">Register</a></li>
                <li><a href="browse.php">View Books</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="featured" style="height: 670px;">
            <h1>Welcome to MyLibrary!</h1>
            <p style="width: 570px;" >An online library to <br> Explore a vast collection of books at your fingertips.</p>
            <!-- Additional content can go here -->
        </section>
    </main>

    <footer>
        <p>&copy; 2023 Online Library. All rights reserved.</p>
    </footer>

    <script src=".\Javascript\HumburgerMenu.js">  </script>
</body>
</html>
