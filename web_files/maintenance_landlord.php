<?php 
include("navbar.php");
include("user_check.php");

// Redirect if user is not a landlord
if ($user_type !== 'Landlord') {
    header('Location: maintenance.php'); 
    exit; 
}

$databasePath = 'database/lts-database.db';
$db = new SQLite3($databasePath);
$user_id = $_SESSION['user_id'] ?? 0; 

// Fetch maintenance requests for properties owned by this landlord
$query = "SELECT m.request_id, m.property_id, m.request_category, m.request_item, m.request_description, m.request_status, p.address 
          FROM MaintenanceRequests m 
          JOIN Properties p ON m.property_id = p.property_id 
          WHERE p.user_id = :userid";

$stmt = $db->prepare($query);
$stmt->bindValue(':userid', $user_id, SQLITE3_INTEGER);
$results = $stmt->execute();
?>

<html>
<head>
    <title>Maintenance</title>
</head>
<body>
<h1>Maintenance Requests</h1>

<h2>Active Requests</h2>
<table border="1">
    <tr>
        <th>Address</th>

        <th>Item</th>
        <th>Description</th>
        <th>Status</th>
        <th>Update</th>
    </tr>
    <?php 
    while ($row = $results->fetchArray(SQLITE3_ASSOC)):
        if ($row['request_status'] !== 'Completed'): ?>
            <tr>
                <form action="update_request.php" method="post">
                    <td><?php echo htmlspecialchars($row['address']); ?></td>

                    <td><?php echo htmlspecialchars($row['request_item']); ?></td>
                    <td><?php echo htmlspecialchars($row['request_description']); ?></td>
                    <td>
                        <select name="status">
                            <option value="Pending" <?php if ($row['request_status'] === 'Pending') echo 'selected'; ?>>Pending</option>
                            <option value="In Progress" <?php if ($row['request_status'] === 'In Progress') echo 'selected'; ?>>In Progress</option>
                            <option value="Completed" <?php if ($row['request_status'] === 'Completed') echo 'selected'; ?>>Completed</option>
                        </select>
                    </td>
                    <td>
                        <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                        <input type="submit" value="Update">
                    </td>
                </form>
            </tr>
        <?php endif;
    endwhile; ?>
</table>

<h2>Completed Requests</h2>
<table border="1">
    <tr>
        <th>Address</th>

        <th>Item</th>
        <th>Description</th>
        <th>Status</th>
    </tr>
    <?php 
    $results->reset(); 
    while ($row = $results->fetchArray(SQLITE3_ASSOC)):
        if ($row['request_status'] === 'Completed'): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['address']); ?></td>

                <td><?php echo htmlspecialchars($row['request_item']); ?></td>
                <td><?php echo htmlspecialchars($row['request_description']); ?></td>
                <td><?php echo htmlspecialchars($row['request_status']); ?></td>
            </tr>
        <?php endif;
    endwhile; ?>
</table>

</body>
</html>
