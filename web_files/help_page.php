<?php
include("navbar.php");
include("user_check.php");

// Array of help topics
$helpTopics = [
    [
        'title' => 'Booking',
        'description' => 'Guide on booking a new property using out website'

    ],
    [
        'title' => 'Payment',
        'description' => 'Information and details about rent payments on the website.',

    ],
    [
        'title' => 'Using Features',
        'description' => 'Detailed guide on using the main features of our website.',

    ],
    [
        'title' => 'Troubleshooting',
        'description' => 'Common issues and how to solve them.',

    ],
    [
        'title' => 'Privacy Policy',
        'description' => 'Information about our privacy policy',

    ],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Help</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="property-grid">
        <?php foreach ($helpTopics as $index => $topic) { ?>
            <div class="property-card">  
                <div class="property-info">
                    <h3><?= htmlspecialchars($topic['title']); ?></h3>
                    <p><?= nl2br(htmlspecialchars($topic['description'])); ?></p>
                    <p><a href="help_details.php?topic_id=<?= $index; ?>">Learn more</a></p>
                </div> 
            </div> <!-- End of property card -->
        <?php } ?>
    </div>
</body>
</html>
