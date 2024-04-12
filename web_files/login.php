<?php include("navbar.php")?>

<?php
// Initialize the database connection
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
            $message = "Login successful!";
            // You can redirect the user to another page or set session variables here
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
    <title>Login Form</title>
</head>
<body>

<h2>Login Form</h2>

<?php if ($message): ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>

<form action="" method="post">
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username" required><br>

    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" required><br>

    <input type="submit" value="Login">
</form>

</body>
</html>
