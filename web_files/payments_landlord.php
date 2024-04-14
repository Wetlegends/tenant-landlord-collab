<?php include("navbar.php");
include("user_check.php")?>
<p>User Type: <?php echo $user_type; ?></p>

<?php
// Redirect if user is a landlord
if ($user_type !== 'Landlord') {
    header('Location: payments.php'); // Redirect to landlord page
    exit; // Stop the script
}
?>