<?php
// Start the session
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

// Include the database connection file
require_once 'db.php';

// Fetch user information
$user_id = $_SESSION["id"];
$username = $_SESSION["username"]; // Assuming you store username in session

// Calculate fine for each overdue book
$calcFineQuery = "UPDATE borrow_records
                  SET fine = DATEDIFF(CURDATE(), return_date) * 0.2
                  WHERE user_id = :user_id AND is_returned = 0";
$calcFineStmt = $db->prepare($calcFineQuery);
$calcFineStmt->execute(['user_id' => $user_id]);

// Handle book return
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['return_book'])) {
    $record_id = $_POST['id'];

    // Update the borrow record to mark the book as returned
    $returnBookQuery = "UPDATE borrow_records SET is_returned = 1 WHERE id = :record_id";
    $stmt = $db->prepare($returnBookQuery);
    $stmt->execute(['record_id' => $record_id]);

    // Update the book availability
    $isbn = $_POST['isbn'];
    $updateBookQuery = "UPDATE books SET available = 1 WHERE isbn = :isbn";
    $updateStmt = $db->prepare($updateBookQuery);
    $updateStmt->execute(['isbn' => $isbn]);

    // Redirect to the profile page or show a success message
    header("Location: profile.php");
    exit;
}

// Fetch the books the user has borrowed including the fine and price
$borrowedBooksQuery = "SELECT br.id as record_id, br.isbn,br.is_wiped, br.borrow_date, br.return_date, br.period, br.fine, b.title, b.author, b.price FROM borrow_records br JOIN books b ON br.isbn = b.isbn WHERE br.user_id = :user_id AND br.is_returned = 0";
$stmt = $db->prepare($borrowedBooksQuery);
$stmt->execute(['user_id' => $user_id]);
$borrowedBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile | Online Library</title>
    <!-- <link rel="stylesheet" href="./CSS/profile.css"> -->
    <link rel="stylesheet" href="./CSS/Browstyles.css">

</head>
<body>
    <div class="profile-container book-list-container"  >
        <h1>Welcome, <?= htmlspecialchars($username); ?></h1>
        <a href="user.php" class="Home-button">Home</a>

        <h2>Borrowed Books</h2>
        <div class="borrowed-books-list book-list book-item ">
            <?php foreach ($borrowedBooks as $book): ?>
                <div class="borrowed-book" data-record-id="<?= htmlspecialchars($book["record_id"]); ?>">
                    <h3><?= htmlspecialchars($book["title"]); ?></h3>
                    <p>Author: <?= htmlspecialchars($book["author"]); ?></p>
                    <p>Borrowed on: <?= htmlspecialchars($book["borrow_date"]); ?></p>
                    <p>Return by: <?= htmlspecialchars($book["return_date"]); ?></p>
                    <?php 
                    $currentdate =new DateTime();
                    if($currentdate >=new DateTime($book["return_date"])  ){
                      echo ' <p>Fine: ' . number_format($book["fine"], 2). ' BHD</p>';
                      if($book['is_wiped'] == 1){
                        echo 'the fine has been wiped !!';
                      }
                    }else{
                        echo ' <p>Fine: 0.0 BHD</p>';
                    }
                    
                    ?>
                   
                    <p>Price: <?= number_format($book["price"], 2); ?> BHD</p>
                    <!-- Form for returning books -->
                    <form action="profile.php" method="post">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($book["record_id"]); ?>">
                        <input type="hidden" name="isbn" value="<?= htmlspecialchars($book["isbn"]); ?>">
                        <input type="submit" name="return_book" value="Return Book">
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Logout button or other profile management features -->
    </div>
    <script src=".\Javascript\timer.js"></script>
</body>
</html>
