<?php
include 'db.php'; // Database connection
session_start(); // Start the session

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

// Pagination settings
$resultsPerPage = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$startingLimitNumber = ($page - 1) * $resultsPerPage;

// Search term
$searchTerm = isset($_GET['search']) ? "%" . htmlspecialchars($_GET['search']) . "%" : "%";

// Total number of books
$queryTotal = "SELECT COUNT(*) FROM books WHERE title LIKE :searchTerm AND available = 1";
$stmtTotal = $db->prepare($queryTotal);
$stmtTotal->execute(['searchTerm' => $searchTerm]);
$totalResults = $stmtTotal->fetchColumn();
$numberOfPages = ceil($totalResults / $resultsPerPage);

// Borrowing functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrow'])) {
    // Assuming the form for borrowing has been submitted with 'isbn' and 'period' fields
    $isbn = $_POST['isbn'];
    $period = $_POST['period'];

    // Convert period to days
    $periodDaysMap = [
        '6 months' => 180,
        '1 month' => 30,
        '1 week' => 7,
    ];

    $periodDays = $periodDaysMap[$period] ?? 0;

    // Check if the book is available for borrowing
    $checkAvailability = "SELECT available FROM books WHERE isbn = :isbn AND available = 1";
    $stmtCheck = $db->prepare($checkAvailability);
    $stmtCheck->bindParam(':isbn', $isbn);
    $stmtCheck->execute();

    if ($stmtCheck->rowCount() == 1) {
        // Book is available, proceed with borrowing
        $borrowBook = "INSERT INTO borrow_records (user_id, isbn, borrow_date, return_date, period) VALUES (:user_id, :isbn, CURDATE(), DATE_ADD(CURDATE(), INTERVAL :periodDays DAY), :period)";
        $stmtBorrow = $db->prepare($borrowBook);
        $stmtBorrow->execute([
            'user_id' => $_SESSION['id'],
            'isbn' => $isbn,
            'periodDays' => $periodDays,
            'period' => $period
        ]);

        // Update book availability
        $updateBook = "UPDATE books SET available = 0 WHERE isbn = :isbn";
        $stmtUpdate = $db->prepare($updateBook);
        $stmtUpdate->bindParam(':isbn', $isbn);
        $stmtUpdate->execute();

        // Redirect to user profile page or display success message
        header("Location: user.php?borrow_success=1");
        exit;
    } else {
        // Book is not available
        $error_message = "This book is currently not available for borrowing.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Books | Online Library</title>
    <link rel="stylesheet" href="./CSS/BrowseReg.css">
</head>
<body>
    <div class="book-list-container">
        <h1 class="page-title">Browse Available Books</h1>
        <div class="home-button-container">
            <a href="user.php" class="home-button">Return Home</a>
        </div>
        <form action="browse.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search for books..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" />
            <button type="submit">Search</button>
        </form>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?= $error_message ?></div>
        <?php endif; ?>
        <div class="book-list">
            <?php
            $query = "SELECT isbn, title, author, publish_year, image_url FROM books WHERE title LIKE :searchTerm AND available = 1 LIMIT :limit, :results_per_page";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':searchTerm', $searchTerm);
            $stmt->bindParam(':limit', $startingLimitNumber, PDO::PARAM_INT);
            $stmt->bindParam(':results_per_page', $resultsPerPage, PDO::PARAM_INT);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='book-item'>";
                echo "<div class='book-image-container'>";
                echo "<img src='" . htmlspecialchars($row['image_url']) . "' alt='Book Image' class='book-image'>";
                echo "</div>";
                echo "<div class='book-info'>";
                echo "<div class='book-title'>" . htmlspecialchars($row['title']) . "</div>";
                echo "<div class='book-author'>Author: " . htmlspecialchars($row['author']) . "</div>";
                echo "<div class='book-publish-year'>Published: " . htmlspecialchars($row['publish_year']) . "</div>";
                // Borrow form for each book
                echo "<form method='POST' class='borrow-form'>";
                echo "<input type='hidden' name='isbn' value='" . htmlspecialchars($row['isbn']) . "'>";
                echo "<select name='period'>";
                echo "<option value='6 months'>6 Months</option>";
                echo "<option value='1 month'>1 Month</option>";
                echo "<option value='1 week'>1 Week</option>";
                echo "</select>";
                echo "<input type='submit' name='borrow' value='Borrow'>";
                echo "</form>";
                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>
        <div class="pagination">
            <?php for ($page = 1; $page <= $numberOfPages; $page++): ?>
                <a href="browseRegistered.php?search=<?= $_GET['search'] ?? '' ?>&page=<?= $page ?>" class="<?= $page == $currentPage ? 'active' : '' ?>">
                    <?= $page ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
