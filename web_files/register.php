<?php include("navbar.php")?>

<?php
// Initialize the database connection
$databasePath = 'database/lts-database.db';
$pdo = new PDO("sqlite:$databasePath");

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_fname = $_POST['user_fname'];
    $user_lname = $_POST['user_lname'];
    $user_email = $_POST['user_email'];
    $user_type = $_POST['user_type'];

    // Generate a random salt
    $salt = bin2hex(random_bytes(16));

    // Hash the password
    $hashed_password = hash('sha256', $password . $salt);

    // Insert data into UserCredentials table
    $stmt = $pdo->prepare("INSERT INTO UserCredentials (username, hashed_password, salt) VALUES (:username, :hashed_password, :salt)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':hashed_password', $hashed_password);
    $stmt->bindParam(':salt', $salt);
    $stmt->execute();

    // Insert data into Users table
    $stmt = $pdo->prepare("INSERT INTO Users (username, user_fname, user_lname, user_email, user_type) VALUES (:username, :user_fname, :user_lname, :user_email, :user_type)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':user_fname', $user_fname);
    $stmt->bindParam(':user_lname', $user_lname);
    $stmt->bindParam(':user_email', $user_email);
    $stmt->bindParam(':user_type', $user_type);
    $stmt->execute();

    echo "Registration successful!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
</head>
<body>

<h2>Registration Form</h2>

<form action="" method="post">
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username" required><br>

    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" required><br>

    <label for="user_fname">First Name:</label><br>
    <input type="text" id="user_fname" name="user_fname" required><br>

    <label for="user_lname">Last Name:</label><br>
    <input type="text" id="user_lname" name="user_lname" required><br>

    <label for="user_email">Email:</label><br>
    <input type="email" id="user_email" name="user_email" required><br>

    <label for="user_type">User Type:</label><br>
    <select id="user_type" name="user_type" required>
        <option value="Tenant">Tenant</option>
        <option value="Landlord">Landlord</option>
    </select><br>

    <input type="submit" value="Register">
</form>

</body>
</html>
