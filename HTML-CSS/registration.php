<?php
// Include the database connection file
require_once 'db.php';

// Function to validate CPR format using regular expressions
function validateCPR($cpr) {
    // Adjust the regex as per the exact CPR format requirements
    return preg_match("/^[0-9]{9}$/", $cpr);
}

// Function to validate strong password using regular expressions
function validatePassword($password) {
    // Password must contain at least one number, one lowercase and one uppercase letter, and at least 8 or more characters
    return preg_match("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/", $password);
}

// Check if the server request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $cpr = trim($_POST['cpr']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate form data
    if (validateCPR($cpr) && filter_var($email, FILTER_VALIDATE_EMAIL) && validatePassword($password)) {
        // Check if CPR or email already exists
        $sql = "SELECT COUNT(*) FROM users WHERE cpr = :cpr OR email = :email";
        $stmt = $db->prepare($sql);
        $stmt->execute([':cpr' => $cpr, ':email' => $email]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            // User exists
            $msg= "User with the given CPR or Email already exists!";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into the database
            $insertSql = "INSERT INTO users  VALUES (NULL,:cpr, :email, :password,0,CURDATE())";
            $insertStmt = $db->prepare($insertSql);
            $success = $insertStmt->execute([
                ':cpr' => $cpr,
                ':email' => $email,
                ':password' => $hashedPassword
            ]);

            if ($success) {
                // Registration successful
                $msg= "Registration successful!";
                // Redirect to login page
                header("Location: login.php");
                exit;
            } else {
                // Error during registration
                $msg= "Error during registration!";
            }
        }
    } else {
        // Invalid data provided
        $msg= "Invalid data provided!";
    }
} 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Online Library</title>
    <link rel="stylesheet" href="./CSS/Regstyles.css">
</head>
<body>
    <div class="registration-container">
        <div class="registration-form">
            <div class="form-header">
                <span class="close-button"><a href="index.php">✕</a></span>
                <h2>• Register Form •</h2>
            </div>
            <form action="" method="post">
                <p ><?php if (isset($msg)){
                    echo $msg;

                }
                ?></p>
                <div class="form-group">
                    <label for="name">CPR</label>
                    <input type="text" id="cpr" onkeyup="vald(this.value)" name="cpr" required pattern="[0-9]{9}">
                    <p id="error"></p>
                
                </div>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" required>
                </div>
      
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
             
                <div class="form-action">
                    
                    <input type="submit" value="CREATE ACCOUNT">
                    <p>Already have an account? <a href="login.php">Sign in</a></p>

                </div>
            </form>
        </div>
    </div>
    <script src="Javascript/Valid.js">





    </script>
</body>
</html>
