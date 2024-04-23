<?php
include("navbar.php");
include("user_check.php");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = $_POST['user_id'];
    $property_id = $_POST['property_id'];
    $payment_amount = $_POST['payment_amount'];
    $payment_description = $_POST['payment_description'];
    $payment_date = date('Y-m-d'); // Today's date for the payment.

    // Prepare an insert statement to the PaymentHistory table.
    $query = "INSERT INTO PaymentHistory (property_id, user_id, payment_date, payment_amount, payment_description) VALUES (:property_id, :user_id, :payment_date, :payment_amount, :payment_description)";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':property_id', $property_id, SQLITE3_INTEGER);
    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(':payment_date', $payment_date, SQLITE3_TEXT);
    $stmt->bindValue(':payment_amount', $payment_amount, SQLITE3_FLOAT);
    $stmt->bindValue(':payment_description', $payment_description, SQLITE3_TEXT);

    if ($stmt->execute()) {
 
        header('Location: payments.php'); 
    } else {

        echo "An error occurred. Please try again.";
    }
} else {
    echo "Invalid request method.";
}
?>
