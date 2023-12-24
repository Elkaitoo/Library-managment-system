<?php
include 'db.php'; 
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)  {
    header("Location: login.php");
    exit;
}

// Database connection
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

// Fetch all books for display
$query = "SELECT isbn, title, author, publish_year, price, image_url FROM books LIMIT :limit, :results_per_page";
$stmt = $db->prepare($query);
$stmt->bindParam(':limit', $startingLimitNumber, PDO::PARAM_INT);
$stmt->bindParam(':results_per_page', $resultsPerPage, PDO::PARAM_INT);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle the book addition form submission
// ... (Add book logic) ...
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_book'])) {
    // Retrieve the book details from the form
    $isbn = $_POST['isbn'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $publish_year = $_POST['publish_year'];
    $price = $_POST['price'];

    // Handle the file upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        // Check if file was uploaded without errors
        // Add your file handling and upload logic here
        // For the sake of example, we're just assuming the file is correctly uploaded
        $image_path = 'Images/Books/' . basename($_FILES["image"]["name"]);
        // You should move the uploaded file to the $image_path using move_uploaded_file()

        // After handling the file upload, insert the book details into the database
        $addBookQuery = "INSERT INTO books (isbn,title, author, publish_year, price, image_url) VALUES (:isbn, :title, :author, :publish_year, :price, :image_url)";
        $stmt = $db->prepare($addBookQuery);
        $stmt->execute([
            'isbn' => $isbn,
            'title' => $title,
            'author' => $author,
            'publish_year' => $publish_year,
            'price' => $price,
            'image_url' => $image_path
        ]);
        $status = " $title has been added successfully!";
     
        // Redirect to the manager page or display a success message
        
    }
}
// Handle the book deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_book'])) {
    $isbn = $_POST['isbn'];

    // Delete book from the database
    $deleteBookQuery = "DELETE FROM books WHERE isbn = :isbn";
    $stmt = $db->prepare($deleteBookQuery);
    $stmt->execute(['isbn' => $isbn]);

    // Redirect to manager page or display success message
    $status= "Book deleted successfully!";
    

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Manager | Online Library</title>
    <link rel="stylesheet" href="./CSS/Browstyles.css">
</head>
<body>
    <div class="book-list-container">
        <h1 class="page-title">Library Manager</h1>
        <div class="home-button-container">
            <a href="AdminHome.php" class="home-button">Return Home</a>
        </div>

        <!-- Book Addition Form -->
        <!-- ... (Add book form HTML) ... -->
        <div class="add-book-form">
            <h2>Add a New Book</h2>
            <span > <h3 class="status">
                    <?php
                    
                    if(isset($status)){
                          echo $status; 
                    }
                
                    
                    ?> </h3>
                </span>
            <form action="EditBooks.php" method="POST" enctype="multipart/form-data">
                 <input type="text" name="isbn" placeholder="Book isbn" pattern="^(?:\d{9}[\dX]|97[89]\d{10,13})$"  title="ISBN-10 or ISBN-13 format required" required />
                <input type="text" name="title" placeholder="Book Title" required />
                <input type="text" name="author" placeholder="Author" required />
                <input type="text" name="publish_year" placeholder="Publish Year" required />
                <input type="text" name="price" placeholder="Price" required />
                <input type="file" name="image" required />
                <input type="submit" name="add_book" value="Add Book" />
            </form>
        </div>
        <div class="book-list">
            <?php foreach ($books as $book): ?>
                <div class='book-item'>
                    <div class='book-image-container'>
                        <img src='<?= htmlspecialchars($book['image_url']) ?>' alt='Book Image' class='book-image'>
                    </div>
                    <div class='book-info'>
                        <div class='book-title'><?= htmlspecialchars($book['title']) ?></div>
                        <div class='book-author'>Author: <?= htmlspecialchars($book['author']) ?></div>
                        <div class='book-publish-year'>Published: <?= htmlspecialchars($book['publish_year']) ?></div>
                        <div class='Price'> Price: <?= htmlspecialchars($book['price']) ?> BHD</div>

                        <form method='POST' class='delete-book-form'>
                            <input type='hidden' name='isbn' value='<?= htmlspecialchars($book['isbn']) ?>'>
                            <input type='submit' name='delete_book' value='Delete' class="delete-book" >
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="pagination">
            <?php for ($page = 1; $page <= $numberOfPages; $page++): ?>
                <a href="EditBooks.php?search=<?= $_GET['search'] ?? '' ?>&page=<?= $page ?>" class="<?= $page == $currentPage ? 'active' : '' ?>">
                    <?= $page ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
