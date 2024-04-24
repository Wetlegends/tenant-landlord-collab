<?php include("navbar.php");
include("user_check.php")?>
<?php
// Redirect if user is a landlord
if ($user_type !== 'Landlord') {
    header('Location: payments.php'); 
    exit; 
}
?>

<?php

$user_id = $_SESSION['user_id'] ?? 0; 


$query = "SELECT p.property_id, p.address, ph.payment_date, ph.payment_amount, ph.payment_description
          FROM Properties p
          INNER JOIN PaymentHistory ph ON p.property_id = ph.property_id
          WHERE p.user_id = :user_id
          ORDER BY ph.payment_date DESC";

$stmt = $db->prepare($query);
$stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$result = $stmt->execute();

// Initialize an array to hold the payment history
$paymentHistory = [];

if ($result) {
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $paymentHistory[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <h3>Payment History</h3>
    <table>
        <thead>
            <tr>
                <th>Address</th>
                <th>Payment Date</th>
                <th>Payment Amount</th>
                <th>Payment Description</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($paymentHistory as $payment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($payment['address']); ?></td>
                    <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                    <td><?php echo htmlspecialchars($payment['payment_amount']); ?></td>
                    <td><?php echo htmlspecialchars($payment['payment_description']); ?></td>
                </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
