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
                <h2>• Log In Form •</h2>
            </div>
            <form action="register.php" method="post">
                <div class="form-group">
                    <label for="name">CPR</label>
                    <input type="text" id="cpr" name="cpr" required pattern="[0-9]{9}">
      
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
             
                <div class="form-action">
                    <input type="submit" value="LOG IN">
                    <p>Dont have an account? <a href="registration.php">Register</a></p>
                </div>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
