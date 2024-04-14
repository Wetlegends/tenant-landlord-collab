<?php include("navbar.php");
include("user_check.php")?>

<?php
// Redirect if user is a landlord
if ($user_type === 'Landlord') {
    header('Location: payments_landlord.php'); // Redirect to landlord page
    exit; // Stop the script
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

// SQL query to fetch user information using JOIN operation
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
    <title>Payment Tabs</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your style.css file -->
</head>
<body>

<h2>Payments</h2>

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
    <br><br>
    <input type="submit" value="Submit" style="display: none;">
    <br><br>
</form>


<!-- ... rest of your HTML code ... -->




<div class="tab">
    <button class="tablinks" onclick="openTab(event, 'PaymentInfo')">Payment Information</button>
    <button class="tablinks" onclick="openTab(event, 'UpcomingPayments')">Upcoming Payments</button>
    <button class="tablinks" onclick="openTab(event, 'PaymentHistory')">Payment History</button>
</div>

<div id="PaymentInfo" class="tabcontent">
    <h3>Payment Information</h3>
    <p>Content for Payment Information tab</p>


</div>






<div id="UpcomingPayments" class="tabcontent">
    <h3>Upcoming Payments</h3>

    <?php

    function calculateMonthlyInstallments($rentStartDate, $rentEndDate) {
         $installments = array();

    $startDate = new DateTime($rentStartDate);
    $endDate = new DateTime($rentEndDate);
    $currentDate = new DateTime();

    $interval = new DateInterval('P1M'); // 1 month interval

    // Check if current date is within rent period
    if ($currentDate >= $startDate && $currentDate <= $endDate) {
        $remainingMonths = $startDate->diff($endDate)->m + ($startDate->diff($endDate)->y * 12);


        while ($startDate <= $endDate) {
            $installment = array(
                'date' => $startDate->format('Y-m-d'),
            );
            $installments[] = $installment;

            $startDate->add($interval);
        }
    }

    return $installments;
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

        $installments = calculateMonthlyInstallments($rentStartDate, $rentEndDate);

        $upcomingPayments = getUpcomingPayments($rentEndDate, $history);

        if (!empty($installments) && !empty($upcomingPayments)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($installments as $installment): ?>
                        <?php foreach ($upcomingPayments as $payment): ?>
                            <tr>
                                <td><?php echo $installment['date']; ?></td>
                                <td><?php echo $payment['payment_amount']; ?></td>
                            </tr>
                        <?php endforeach; ?>
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
        // Declare all variables
        let i, tabcontent, tablinks;

        // Get all elements with class="tabcontent" and hide them
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Get all elements with class="tablinks" and remove the class "active"
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        // Show the current tab, and add an "active" class to the button that opened the tab
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // Open the default tab
    document.getElementsByClassName("tablinks")[0].click();
</script>

</body>
</html>

