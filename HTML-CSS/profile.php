<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

$user_id = $_SESSION["id"];
$username = $_SESSION["username"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['return_book'])) {
    $record_id = $_POST['record_id'];
    $isbn = $_POST['isbn'];

    $db->beginTransaction();
    try {
        $returnBookQuery = "UPDATE borrow_records SET is_returned = 1 WHERE id = :record_id";
        $stmt = $db->prepare($returnBookQuery);
        $stmt->execute(['record_id' => $record_id]);

        $updateBookQuery = "UPDATE books SET available = 1 WHERE isbn = :isbn";
        $updateStmt = $db->prepare($updateBookQuery);
        $updateStmt->execute(['isbn' => $isbn]);

        $db->commit();
        header("Location: profile.php");
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        echo "Error: " . $e->getMessage();
    }
}

$borrowedBooksQuery = "SELECT br.id as record_id, br.isbn, br.borrow_date, br.return_date, br.period, b.title, b.author FROM borrow_records br JOIN books b ON br.isbn = b.isbn WHERE br.user_id = :user_id AND br.is_returned = 0";
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
            <h3><?= htmlspecialchars($book["title"]); ?></h3>
            <p>Author: <?= htmlspecialchars($book["author"]); ?></p>
            <p>Borrowed on: <?= htmlspecialchars($book["borrow_date"]); ?></p>
            <p>Return by: <span class="return-date"><?= htmlspecialchars($book["return_date"]); ?></span></p>
            <div class="countdown-timer" id="timer-<?= $book["record_id"]; ?>"></div>
            <form action="profile.php" method="post">
                <!-- ... -->
            </form>
        </div>
    <?php endforeach; ?>
        </div>

        <div class="logout-button-container">        </div>
    </div>

    <script src=".\Javascript\timer.js"> 
    </script>

</body>
</html>
