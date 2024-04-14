<?php
session_start(); // Start the session

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
?>
