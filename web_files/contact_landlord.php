<?php
include("navbar.php");
include("user_check.php");
?>

<?php
$databasePath = 'database/lts-database.db';
$db = new SQLite3($databasePath);
$user_id = $_SESSION['user_id'] ?? 0; 



?>