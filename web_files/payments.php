<?php include("navbar.php");
include("user_check.php")?>

<?php
// Redirect if user is a landlord
if ($user_type === 'Landlord') {
    header('Location: payments_landlord.php'); // Redirect to landlord page
    exit; 
}
?>


<?php
// Database connection for SQLite
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
    $user_id = $user['user_id'];

    // Get properties with addresses for the fetched user_id
    $queryProperties = "SELECT pr.rental_id, pr.property_id, p.address, pr.rent_start, pr.rent_end
                        FROM PropertyRental AS pr
                        JOIN Properties AS p ON pr.property_id = p.property_id
                        WHERE pr.user_id = :user_id";
    
    $stmtProperties = $db->prepare($queryProperties);
    $stmtProperties->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $properties = $stmtProperties->execute();

    // Get payment history based on selected rental_id
    if (isset($_POST['property'])) {
        $selectedRentalId = $_POST['property'];

        $queryHistory = "SELECT ph.payment_id, ph.payment_date, ph.payment_amount, ph.payment_description
                         FROM PaymentHistory AS ph
                         JOIN PropertyRental AS pr ON ph.property_id = pr.property_id
                         WHERE ph.user_id = :user_id AND pr.rental_id = :rental_id";
                        
        $stmtHistory = $db->prepare($queryHistory);
        $stmtHistory->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $stmtHistory->bindValue(':rental_id', $selectedRentalId, SQLITE3_INTEGER);
        $history = $stmtHistory->execute();
    }
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>Payments</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>


<form action="payments.php" method="post">
    <label for="property">Select Property:</label>
    <select name="property" id="property" onchange="this.form.submit()">
        <option value="" <?php if (!isset($_POST['property'])) echo 'selected'; ?>>Select Property</option>
        <?php while ($row = $properties->fetchArray(SQLITE3_ASSOC)): ?>
            <option value="<?php echo $row['rental_id']; ?>" <?php if (isset($_POST['property']) && $_POST['property'] == $row['rental_id']) echo 'selected'; ?>>
                <?php echo $row['address']; ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>




<div class="tab">
    <button class="tablinks" onclick="openTab(event, 'PaymentInfo')">New Payment</button>
    <button class="tablinks" onclick="openTab(event, 'UpcomingPayments')">Upcoming Payments</button>
    <button class="tablinks" onclick="openTab(event, 'PaymentHistory')">Payment History</button>
</div>

<div id="PaymentInfo" class="tabcontent">
    <h3>Payment Information</h3>
    <form action="process_payment.php" method="post" id="paymentForm">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <input type="hidden" name="property_id" value="<?php echo isset($_POST['property']) ? $_POST['property'] : ''; ?>">
        <div>
            <label for="card_number">Card Number:</label>
            <input type="text" id="card_number" name="card_number" required>
            <br>
        </div>
        <div>
            <label for="card_expiry">Expiry Date:</label>
            <input type="text" id="card_expiry" name="card_expiry" placeholder="MM/YY" required>
            <br>
        </div>
        <div>
            <label for="card_cvv">CVV:</label>
            <input type="text" id="card_cvv" name="card_cvv" required>
            <br>
        </div>
        <div>
            <label for="payment_amount">Amount:</label>
            <input type="number" id="payment_amount" name="payment_amount" required>
            <br>
        </div>
        <div>
            <label for="payment_description">Payment Description:</label>
            <textarea id="payment_description" name="payment_description" required></textarea>
            <br>
        </div>
        <button type="submit">Submit Payment</button>
    </form>
</div>





<div id="UpcomingPayments" class="tabcontent">
    <h3>Upcoming Payments</h3>

    <?php

function calculateMonthlyInstallments($rentStartDate, $rentEndDate, $historyArray) {
    $installments = array();

    $startDate = new DateTime($rentStartDate);
    $endDate = new DateTime($rentEndDate);
    $currentDate = new DateTime();

    
    $paymentsMade = array();
    foreach ($historyArray as $payment) {
        $paymentDate = new DateTime($payment['payment_date']);
        $paymentsMade[$paymentDate->format('Y-m')] = true;
    }

    $interval = new DateInterval('P1M'); // 1 month interval
    while ($startDate <= $endDate) {
        if ($startDate < $currentDate) {
            $startDate->add($interval);
            continue;
        }
        $installmentDate = $startDate->format('Y-m');

        // Check if a payment has been made for this month and year
        if (!isset($paymentsMade[$installmentDate])) {
            $installments[] = array(
                'date' => $startDate->format('Y-m-d'), // Format date for display
            );
        }
        $startDate->add($interval);
    }

    return $installments;
}

$historyArray = [];
while ($row = $history->fetchArray(SQLITE3_ASSOC)) {
    $historyArray[] = $row;
}



function getUpcomingPayments($rentEndDate, $history) {
    $upcomingPayments = array();
    $endDate = new DateTime($rentEndDate);
    $currentDate = new DateTime();

    while ($row = $history->fetchArray(SQLITE3_ASSOC)) {
        $paymentDate = new DateTime($row['payment_date']);

        if ($paymentDate > $currentDate && $paymentDate <= $endDate) {
            $upcomingPayments[] = $row;
        }
    }

    return $upcomingPayments;
}





    if (isset($_POST['property'])) {
        $selectedRentalId = $_POST['property'];
        $row = $properties->fetchArray(SQLITE3_ASSOC);
        $rentStartDate = $row['rent_start'];
        $rentEndDate = $row['rent_end'];


$installments = calculateMonthlyInstallments($rentStartDate, $rentEndDate, $historyArray);
$upcomingPayments = getUpcomingPayments($rentEndDate, $history); 


while ($row = $history->fetchArray(SQLITE3_ASSOC)): ?>
    <tr>
        
        <td><?php $defaultPaymentAmount = $row['payment_amount']; ?></td>
    </tr>
<?php endwhile; ?>

<?php

if (!empty($installments)): ?>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($installments as $installment): ?>
                <tr>
                    <td><?php echo $installment['date']; ?></td>
                    <td><?php echo isset($upcomingPayments[0]) ? $upcomingPayments[0]['payment_amount'] : $defaultPaymentAmount; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No upcoming payments available.</p>
<?php endif;
    }
    ?>
</div>


<div id="PaymentHistory" class="tabcontent">
    <h3>Payment History</h3>
    <table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Amount</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $history->fetchArray(SQLITE3_ASSOC)): ?>
            <tr>
                
                <td><?php echo $row['payment_date']; ?></td>
                <td><?php echo $row['payment_amount']; ?></td>
                <td><?php echo $row['payment_description']; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>

<script>
    function openTab(evt, tabName) {
        
        let i, tabcontent, tablinks;

        
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }

  
    document.getElementsByClassName("tablinks")[0].click();
</script>

</body>
</html>