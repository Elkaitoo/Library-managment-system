<?php
session_start(); // Start the session

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

// Handle book return
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['return_book'])) {
    $record_id = $_POST['record_id'];
    $isbn = $_POST['isbn'];
    
    // Calculate fine
    $fineQuery = "SELECT DATEDIFF(CURDATE(), return_date) AS late_days FROM borrow_records WHERE id = :record_id AND is_returned = 0";
    $stmtFine = $db->prepare($fineQuery);
    $stmtFine->execute(['record_id' => $record_id]);
    $fineResult = $stmtFine->fetch(PDO::FETCH_ASSOC);
    $lateDays = $fineResult['late_days'];
    $fine = max(0, $lateDays) * 0.2; // Fine is 0.2 BHD for each day late

    // Update the borrow record to mark the book as returned and update fine
    $returnBookQuery = "UPDATE borrow_records SET is_returned = 1, fine = :fine WHERE id = :record_id";
    $stmt = $db->prepare($returnBookQuery);
    $stmt->execute(['record_id' => $record_id, 'fine' => $fine]);

    // Update the book availability
    $updateBookQuery = "UPDATE books SET available = 1 WHERE isbn = :isbn";
    $updateStmt = $db->prepare($updateBookQuery);
    $updateStmt->execute(['isbn' => $isbn]);

    // Redirect to the profile page or show a success message
    header("Location: profile.php?return_success=1");
    exit;
}

// Fetch the books the user has borrowed
$borrowedBooksQuery = "SELECT br.id as record_id, br.isbn, br.borrow_date, br.return_date, br.period, br.fine, b.title, b.author, b.price FROM borrow_records br JOIN books b ON br.isbn = b.isbn WHERE br.user_id = :user_id AND br.is_returned = 0";
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
    <link rel="stylesheet" href="./CSS/profile.css">
</head>
<body>
    <div class="profile-container">
        <h1>Welcome, <?= htmlspecialchars($username); ?></h1>
        <a href="user.php" class="Home-button">Home</a>



            <h2>Borrowed Books</h2>
        <div class="borrowed-books-list">
        <?php foreach ($borrowedBooks as $book): ?>
        <div class="borrowed-book" data-return-date="<?= htmlspecialchars($book["return_date"]); ?>">
        <form action="" method="post">
            <h3><?= htmlspecialchars($book["title"]); ?></h3>
            <p>Author: <?= htmlspecialchars($book["author"]); ?></p>
            <p>Price: <?= htmlspecialchars($book["price"]); ?> BHD</p>
            <input hidden name="id" value="<?php echo $book["record_id"] ;?>" >
            <input hidden name="isbn" value="<?php echo $book["isbn"] ;?>" >
            <p>Borrowed on: <?= htmlspecialchars($book["borrow_date"]); ?></p>
            <p>Return by: <span class="return-date"><?= htmlspecialchars($book["return_date"]); ?></span></p>
            <p>Fine: <?= htmlspecialchars(number_format($book["fine"], 2)); ?> BHD</p>
             <input type="submit" name="return_book" value="Return Book">
            <div class="countdown-timer" id="timer-<?= $book["id"]; ?>"></div>
            </form>
           
        </div>
    <?php endforeach; ?>

        </div>
    </div>
    <script src=".\Javascript\timer.js"></script>
</body>
</html>

