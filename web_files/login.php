<?php 
include("navbar.php");
?>


<?php


$databasePath = 'database/lts-database.db';
$pdo = new PDO("sqlite:$databasePath");

$message = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch salt and hashed password from UserCredentials table
    $stmt = $pdo->prepare("SELECT hashed_password, salt FROM UserCredentials WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $hashed_password = hash('sha256', $password . $result['salt']);
        
        // Verify the hashed password
        if ($hashed_password === $result['hashed_password']) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $message = "Login successful!";
            header('Location: index.php'); // Redirect to the home page or another page
            exit; 
        } else {
            $message = "Invalid username or password!";
        }
    } else {
        $message = "Invalid username or password!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>

<h2>Login Form</h2>

<?php if ($message): ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>

<form action="" method="post">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br><br>

    <input type="submit" value="Login">
<br><br>
    <a href="register.php" class="btn btn-primary">Register</a>
</form>

<br>




</body>
</html>

