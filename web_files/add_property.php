<?php
session_start();


if ($_SESSION['user_type'] != 'Landlord') {
    echo "Unauthorized access.";
    exit;
}

$databasePath = 'database/lts-database.db';
$db = new SQLite3($databasePath);

$today = date("Y-m-d");

$stmt = $db->prepare('INSERT INTO Properties (user_id, address, rental_price, property_type, property_details, property_listed) VALUES (:user_id, :address, :rental_price, :property_type, :property_details, :property_listed)');
$stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
$stmt->bindValue(':address', $_POST['address'], SQLITE3_TEXT);
$stmt->bindValue(':rental_price', $_POST['rental_price'], SQLITE3_FLOAT);
$stmt->bindValue(':property_type', $_POST['property_type'], SQLITE3_TEXT);
$stmt->bindValue(':property_details', $_POST['property_details'], SQLITE3_TEXT);
$stmt->bindValue(':property_listed', $today, SQLITE3_TEXT); 

if ($stmt->execute()) {
    echo "New property added successfully.";
} else {
    echo "Error adding new property.";
}

header('Location: properties.php');

$db->close();
?>
