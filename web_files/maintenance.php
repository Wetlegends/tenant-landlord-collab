<?php
include("navbar.php");
include("user_check.php"); 


// Redirect if user is a landlord
if ($user_type === 'Landlord') {
    header('Location: maintenance_landlord.php'); 
    exit; 
}

function fetchTenantProperties($userId) {
    global $db;
    $stmt = $db->prepare('SELECT pr.rental_id, pr.property_id, p.address FROM PropertyRental pr JOIN Properties p ON pr.property_id = p.property_id WHERE pr.user_id = ? AND pr.rent_status = "Active"');
    $stmt->bindValue(1, $userId, SQLITE3_INTEGER);
    $result = $stmt->execute();
    return $result;
}

$userId = $_SESSION['user_id'] ?? null; // Fetch the user ID from session

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_maintenance'])) {
    $message = SQLite3::escapeString($_POST['message']);
    $repair_type = SQLite3::escapeString($_POST['repair_type']);
    $property_id = SQLite3::escapeString($_POST['property']);

    $stmt = $db->prepare("INSERT INTO MaintenanceRequests (property_id, user_id, request_category, request_item, request_description, request_status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bindValue(1, $property_id, SQLITE3_INTEGER);
    $stmt->bindValue(2, $userId, SQLITE3_INTEGER);
    $stmt->bindValue(3, 'Maintenance', SQLITE3_TEXT);
    $stmt->bindValue(4, $repair_type, SQLITE3_TEXT);
    $stmt->bindValue(5, $message, SQLITE3_TEXT);
    $stmt->bindValue(6, 'Pending', SQLITE3_TEXT);

    if ($stmt->execute()) {
        echo "<p>Request sent successfully.</p>";
    } else {
        echo "<p>Error: " . $db->lastErrorMsg() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Request</title>
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="property">Select Property:</label>
        <select name="property" id="property" required>
            <option value="">Select Property</option>
            <?php
            $properties = fetchTenantProperties($userId);
            while ($row = $properties->fetchArray(SQLITE3_ASSOC)): ?>
                <option value="<?php echo $row['property_id']; ?>">
                    <?php echo htmlspecialchars($row['address']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label for="message">Message:</label><br>
        <textarea id="message" name="message" rows="4" cols="50" required></textarea><br><br>
        <label for="repair_type">Select repair type:</label>
        <select id="repair_type" name="repair_type" required>
            <option value="Electrical Repair">Electrical Repair</option>
            <option value="Plumbing Repair">Plumbing Repair</option>
            <option value="Heating Repair">Heating Repair</option>
            <option value="Painting">Painting</option>
            <option value="Furniture">Furniture</option>
        </select><br><br>
        <input type="submit" name="submit_maintenance" value="Submit">
    </form>
</body>
</html>
