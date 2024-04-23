<?php
session_start(); 

// Unset all of the session variables
$_SESSION = array();


session_destroy();

// Redirect to the login page
header("Location: login.php");
exit;
?>
