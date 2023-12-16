<?php
include 'db.php'; // Database connection

// Pagination settings
$resultsPerPage = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$startingLimitNumber = ($page - 1) * $resultsPerPage;

// Search term
$searchTerm = isset($_GET['search']) ? "%".htmlspecialchars($_GET['search'])."%" : "%";

// Total number of books
$queryTotal = "SELECT COUNT(*) FROM books WHERE title LIKE :searchTerm AND available = 1";
$stmtTotal = $db->prepare($queryTotal);
$stmtTotal->execute(['searchTerm' => $searchTerm]);
$totalResults = $stmtTotal->fetchColumn();
$numberOfPages = ceil($totalResults / $resultsPerPage);




?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Books | Online Library</title>
    <link rel="stylesheet" href="./CSS/Browstyles.css">
</head>
<body>
    <div class="book-list-container">

        <h1 class="page-title">Browse Available Books</h1>
        <div class="home-button-container">
            <a href="index.php" class="home-button">Return Home</a>
        </div>
        <form action="browse.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search for books..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" />
            <button type="submit">Search</button>
        </form>
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
                $img = $row['image_url'];
                echo "<img src='$img' alt='Book Image' class='book-image'>";
                echo "</div>";
                echo "<div class='book-info'>";
                echo "<div class='book-title'>" . htmlspecialchars($row['title']) . "</div>";
                echo "<div class='book-author'>Author: " . htmlspecialchars($row['author']) . "</div>";
                echo "<div class='book-publish-year'>Published: " . htmlspecialchars($row['publish_year']) . "</div>";
                echo "<a href='registration.php' class='borrow-button'>Borrow</a>";
                echo "</div>";
                echo "</div>";
                
            }
            ?>

        </div>
        <div class="pagination">
            <?php for ($page = 1; $page <= $numberOfPages; $page++): ?>
                <a href="browse.php?search=<?= $_GET['search'] ?? '' ?>&page=<?= $page ?>" class="<?= $page == $currentPage ? 'active' : '' ?>">
                    <?= $page ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
