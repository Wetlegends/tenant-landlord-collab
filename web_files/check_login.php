<?php
session_start(); // Start the session

// Initialize $loggedin to false
$loggedin = false;

// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// Check if user is not logged in and not on the login or register page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // If the user is trying to access pages other than login or register, redirect them
    if ($current_page != 'login.php' && $current_page != 'register.php') {
        header('Location: login.php'); // Redirect to login page
        exit; 
    }
} else {

    // If user is logged in, set $loggedin to true
    $loggedin = true;

    $databasePath = 'database/lts-database.db';
    $db = new SQLite3($databasePath);

    if (!$db) {
        die("Failed to connect to database");
    }

    // Check if the username is stored in the session
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];  

        $query = "SELECT user_id, user_type
                  FROM Users
                  WHERE username = :username";

        $stmt = $db->prepare($query);
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);

        $result = $stmt->execute();

        if ($result) {
            $user = $result->fetchArray(SQLITE3_ASSOC);
            if ($user) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_type'] = $user['user_type'];
            } else {
                // If no user data is found, handle the error or force logout
                echo "No user data available. Logging out.";

                $_SESSION = array();
                session_destroy();
                // Redirect to login page
                header('Location: login.php');
                exit;
            }
        }
    } else {
        echo "Username not set in session. Logging out.";

        $_SESSION = array();
        session_destroy();
        // Redirect to login page
        header('Location: login.php');
        exit;
    }
}



?>

