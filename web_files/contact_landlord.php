<?php 
include("navbar.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Landlord</title>
</head>
<body>
    <h2>Contact Landlord</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="message">Message:</label><br>
        <textarea id="message" name="message" rows="4" cols="50"></textarea><br><br>
        <label for="repair_type">Select repair type:</label>
        <select id="repair_type" name="repair_type">
            <option value="Electrical Repair">Electrical Repair</option>
            <option value="Plumbing Repair">Plumbing Repair</option>
            <option value="Heating Repair">Heating Repair</option>
            <option value="Painting">Painting</option>
        </select><br><br>
        <input type="submit" value="Submit">
    </form>
</body>
</html>

<?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $db = new SQLite3('database/lts-database.db');
            $message = SQLite3::escapeString($_POST['message']);
            $repair_type = SQLite3::escapeString($_POST['repair_type']);
            $request_category = "Maintenance";
            $request_status = "Pending";
            $request_id = "11";
            $property_id = $request_id;
            $user_id = "test";
            

            $sql = "INSERT INTO MaintenaceRequests (request_id, property_id, user_id, request_category, request_item, request_description, request_status) VALUES ('$request_id', '$property_id', '$user_id', '$request_category', '$repair_type', '$message', '$request_status')";
            if ($db->exec($sql)) {
                echo "sent succesfully";
            } else {
                echo "Error: " . $db->lastErrorMsg();
            }
            $db->close();
        }
        ?>