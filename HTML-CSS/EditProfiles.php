<?php
session_start();

// Include the database connection file
require_once 'db.php';

// Check if the user is an admin
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["is_admin"] != 1){
    header("Location: login.php");
    exit;
}


// Handle wipe fees
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['wipe_fees'])) {
    $record_id = $_POST['record_id'];

    $wipeFeesQuery = "UPDATE borrow_records SET is_wiped = '1' WHERE id = :record_id";
    $stmt = $db->prepare($wipeFeesQuery);
    $stmt->execute(['record_id' => $record_id]);

   $status = "Fees wiped for record ID: $record_id"
   ;
}

// Handle force return
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['force_return'])) {
    $record_id = $_POST['record_id'];
    $isbn = $_POST['isbn'];

    // Update the borrow record to mark the book as returned
    $returnBookQuery = "UPDATE borrow_records SET is_returned = 1 WHERE id = :record_id";
    $stmt = $db->prepare($returnBookQuery);
    $stmt->execute(['record_id' => $record_id]);

    // Update the book availability
    $updateBookQuery = "UPDATE books SET available = 1 WHERE isbn = :isbn";
    $updateStmt = $db->prepare($updateBookQuery);
    $updateStmt->execute(['isbn' => $isbn]);

    $status = "Book returned for record ID: $record_id";
}

// Fetch all borrow records
$fetchAllBorrowsQuery = "SELECT br.id, br.isbn, br.fine, b.title FROM borrow_records br JOIN books b ON br.isbn = b.isbn WHERE br.is_returned = 0";
$stmt = $db->prepare($fetchAllBorrowsQuery);
$stmt->execute();
$allBorrows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profiles | Admin</title>
    <link rel="stylesheet" href="./CSS/Editprofiles.css">
    <link rel="stylesheet" href="./CSS/Browstyles.css">
    <style>

.borrow-records {
    flex-direction: column;
}
    </style>
</head>
<body>
    <div class="admin-container book-list-container">
        <h1>Admin - Edit User Profiles</h1>
        <a href="AdminHome.php" class="Home-button">Home</a>
        <a style="
          text-align: center;
    margin-left: 20px;
    color: #ffffff;
        
        ">
                    <?php
                    
                    if(isset($status)){
                        echo $status;
                    }
                
                    
                    ?>
                </a>
        <div class="borrow-records book-list book-item">
            <?php foreach ($allBorrows as $borrow): ?>
                <div class="borrow-record">
                    <p>Record ID: <?= htmlspecialchars($borrow["id"]); ?></p>
                    <p>Title: <?= htmlspecialchars($borrow["title"]); ?></p>
                    
                    <p>Fine: <?= number_format($borrow["fine"], 2); ?> BHD</p>

              
                    <!-- Wipe Fees Button -->
                    <form action="" method="post">
                        <input type="hidden" name="record_id" value="<?= htmlspecialchars($borrow["id"]); ?>">
                        <input type="submit" name="wipe_fees" value="Wipe Fees">
                    </form>
                    <!-- Force Return Button -->
                    <form action="" method="post">
                        <input type="hidden" name="record_id" value="<?= htmlspecialchars($borrow["id"]); ?>">
                        <input type="hidden" name="isbn" value="<?= htmlspecialchars($borrow["isbn"]); ?>">
                        <input type="submit" name="force_return" value="Force Return">
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
