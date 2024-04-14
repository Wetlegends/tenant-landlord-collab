<?php include("navbar.php")?>


<!-- Visual Indicator for Login Status -->
<div class="login-status">
    <?php if ($loggedin): ?>
        <p class="logged-in">You are logged in as <?php echo $_SESSION['username']; ?>. <a href="logout.php">Logout</a></p>
    <?php else: ?>
        <p class="logged-out">You are not logged in. <a href="login.php">Login</a></p>
    <?php endif; ?>
</div>




<?php
// Database connection
$databasePath = 'database/lts-database.db';
$db = new SQLite3($databasePath);

if (!$db) {
    die("Failed to connect to database");
}

// Username from UserCredentials table
$username = $_SESSION['username'];  // You can replace this with the actual username

// SQL query to fetch user information using JOIN operation
$query = "SELECT u.user_id, u.user_fname, u.user_lname, u.user_email, u.user_type
          FROM Users AS u
          JOIN UserCredentials AS uc ON u.username = uc.username
          WHERE uc.username = :username";

$stmt = $db->prepare($query);
$stmt->bindValue(':username', $username, SQLITE3_TEXT);

$result = $stmt->execute();

if ($result) {
    $user = $result->fetchArray(SQLITE3_ASSOC);
    
    if ($user) {
        // Display user information
        echo "User ID: " . $user['user_id'] . "<br>";
        echo "First Name: " . $user['user_fname'] . "<br>";
        echo "Last Name: " . $user['user_lname'] . "<br>";
        echo "Email: " . $user['user_email'] . "<br>";
        echo "User Type: " . $user['user_type'] . "<br>";
    } else {
        echo "No user found with the given username.";
    }
} else {
    echo "Error executing query: " . $db->lastErrorMsg();
}

// Close the database connection
$db->close();
?>
