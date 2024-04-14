<?php
//session_start(); // Start the session

// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// Check if user is not logged in and not on the login or register page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    if ($current_page != 'login.php' && $current_page != 'register.php') {
        header('Location: login.php'); // Redirect to login page
        exit; // Stop the script
    }
}

// Set the $loggedin variable
$loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Database connection for SQLite
$databasePath = 'database/lts-database.db';
$db = new SQLite3($databasePath);

if (!$db) {
    die("Failed to connect to database");
}

// Username from UserCredentials table
$username = $_SESSION['username'];  

// SQL query to fetch user information
$query = "SELECT user_type
          FROM Users
          WHERE username = :username";

$stmt = $db->prepare($query);
$stmt->bindValue(':username', $username, SQLITE3_TEXT);

$result = $stmt->execute();

if ($result) {
    $user = $result->fetchArray(SQLITE3_ASSOC);
    $user_type = $user['user_type']; // 'landlord' or 'tenant'
}
?>


