<?php include("navbar.php");
include("user_check.php")?>

<?php

echo '<!DOCTYPE html>';
echo '<title>Property Listings</title>';
echo '<link rel="stylesheet" href="style.css">'; 
echo '</head>';
echo '<body>';

$databasePath = 'database/lts-database.db';
$db = new SQLite3($databasePath);



$sql = "SELECT Properties.*, Users.user_email, Users.user_fname, Users.user_lname
        FROM Properties
        LEFT JOIN PropertyRental ON Properties.property_id = PropertyRental.property_id
        LEFT JOIN Users ON Properties.user_id = Users.user_id
        WHERE PropertyRental.property_id IS NULL";

$result = $db->query($sql);

echo '<div class="property-grid">'; 

if ($result) {
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        echo '<div class="property-card">';
        $imagePath = !empty($row['image_path']) ? $row['image_path'] : 'path/to/default/image.jpg';
        echo "<img src='{$imagePath}' alt='Property Image' class='property-image'>";
        echo '<div class="property-info">';
        echo '<h3>' . htmlspecialchars($row["address"]) . '</h3>';
        echo '<p>Price Monthly: £' . htmlspecialchars($row["rental_price"]) . '</p>';
        echo '<p>Type: ' . htmlspecialchars($row["property_type"]) . '</p>';
        echo '<p>Details: ' . htmlspecialchars($row["property_details"]) . '</p>';
     
        echo '<p><a href="mailto:' . htmlspecialchars($row["user_email"]) . '">Contact Landlord</a></p>';
        echo '</div>';

        
        if ($_SESSION['user_type'] == 'Landlord') {
            echo '<form action="upload_image.php" method="post" enctype="multipart/form-data">
                    Select image to upload:
                    <input type="file" name="fileToUpload" id="fileToUpload">
                    <input type="hidden" name="property_id" value="' . $row['property_id'] . '">
                    <input type="submit" value="Upload Image" name="submit">
                  </form>';
        }

        echo '</div>'; 
    }
} else {
    echo "<p>No available properties</p>";
}

echo '</div>'; 

// Only show the form to landlords
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'Landlord') {
    echo '<h2>Add New Property</h2>';
    echo '<form action="add_property.php" method="post">
            Address: <input type="text" name="address" required><br>
            Rental Price (£): <input type="number" name="rental_price" required><br>
            Property Type: <input type="text" name="property_type" required><br>
            Property Details: <textarea name="property_details" required></textarea><br>
            <input type="submit" value="Add Property">
          </form>';
}

$db->close();

echo '</body>';
echo '</html>';
?>
