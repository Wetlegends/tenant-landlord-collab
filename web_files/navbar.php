<?php 
include("check_login.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="index.php" class="logo">Logo</a>
        <ul class="nav-menu">
            <li><a href="index.php" class="nav-link">Home</a></li>
            <li><a href="payments.php" class="nav-link">Payments</a></li>
            <li><a href="#" class="nav-link">Maintenance</a></li>
            <li><a href="#" class="nav-link">Bookings</a></li>
            <li><a href="#" class="nav-link">Help</a></li>
            <li><a href="contact_landlord.php" class="nav-link">Contact Landlord</a></li>
            <?php if ($loggedin): ?>
                <li><a href="profile.php" class="nav-link">Profile</a></li>
            <?php else: ?>
                <li><a href="login.php" class="nav-link">Login</a></li>
            <?php endif; ?>
          
            
        </ul>
    </div>
</nav>

