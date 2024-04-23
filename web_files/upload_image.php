<?php

$databasePath = 'database/lts-database.db';
$db = new SQLite3($databasePath);

// Define the target directory for uploaded images
$target_dir = "uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Get the property ID from the hidden form field
$property_id = intval($_POST['property_id']);

// Handle the file upload
if (isset($_FILES["fileToUpload"])) {
    $imageFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));

    // Create a unique file name for the uploaded file
    $newFileName = "property_" . $property_id . "_" . time() . "." . $imageFileType;
    $target_file = $target_dir . $newFileName;

    $uploadOk = 1;

    // Check if image file is an actual image
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size 
    if ($_FILES["fileToUpload"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Attempt to upload the file
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "The file " . htmlspecialchars($newFileName) . " has been uploaded.";

            // Update the database with the new image path
            $sql = "UPDATE Properties SET image_path = '$target_file' WHERE property_id = $property_id";
            if ($db->exec($sql)) {
                echo "The property image has been updated successfully.";
            } else {
                echo "Error: Could not update the property image in the database.";
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
} else {
    echo "No file was uploaded.";
}

$db->close();
?>


<?php
header('Location: properties.php');
?>