<?php include("navbar.php")?>
<link rel="stylesheet" href="styles.css">


<?php
// Database connection
$databasePath = 'database/lts-database.db';
$db = new SQLite3($databasePath);

if (!$db) {
    die("Failed to connect to database");
}

// Username from UserCredentials table
$username = $_SESSION['username'];  


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
        echo '<div class="user-profile">';
        echo "<div class='logged-in'>You are logged in as " .$_SESSION['username'] . ' '. '<a href="logout.php">Logout</a></p>'; 
        echo "<div class='user-detail'><strong>User ID:</strong> " . $user['user_id'] . "</div>";
        echo "<div class='user-detail'><strong>First Name:</strong> " . $user['user_fname'] . "</div>";
        echo "<div class='user-detail'><strong>Last Name:</strong> " . $user['user_lname'] . "</div>";
        echo "<div class='user-detail'><strong>Email:</strong> " . $user['user_email'] . "</div>";
        echo "<div class='user-detail'><strong>User Type:</strong> " . $user['user_type'] . "</div>";
        
        echo '</div>';
    } else {
        echo '<p class="error">No user found with the given username.</p>';
    }
   
    
} else {
    echo "Error executing query: " . $db->lastErrorMsg();
}


$db->close();
?>
