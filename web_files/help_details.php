<?php
include("navbar.php");
include("user_check.php");


$helpTopics = [
    ['title' => 'Booking', 'fullDescription' =>'
    Booking a propety on the website is simple, just head to the <a href="properties.php">properties</a> page 
    and find a property that intrests you, then at the bottom of that property card you 
    should find a "Contact Landlord" button which will open an email link to put you in 
    touch with the landlord releveant to your selected property.
  '
    
    ],

    ['title' => 'Payment', 'fullDescription' => 'How to pay...'],

    ['title' => 'Using Features', 'fullDescription' => 'Guide on using features...'],

    ['title' => 'Troubleshooting', 'fullDescription' => 'Common issues and solutions...'],

    ['title' => 'Privacy Policy', 'fullDescription' => <<<EOD
    1. Information Collection
    We collect information from you when you register on our site, log in to your account, make a transaction, and/or log out. The information collected includes your name, email address, and password.\n
    2. Use of Information
    The information we collect from you may be used to:
    •	Personalize your experience and respond to your individual needs
    •	Improve our website offerings based on your feedback
    •	Contact you via email for updates or promotional offers
    •	Administer a contest, survey, or other site feature\n
    3. Information Protection
    We implement a variety of security measures to maintain the safety of your personal information. Your personal information is contained behind secured networks and is only accessible by a limited number of persons who have special access rights and are required to keep the information confidential. All sensitive information you supply is safely encrpyed.\n
    4. Information Disclosure
    We do not sell, trade, or otherwise transfer your personally identifiable information to outside parties. This does not include trusted third parties who assist us in operating our website, conducting our business, or servicing you, so long as those parties agree to keep this information confidential.\n
    5. Consent
    By using our site, you consent to our privacy policy.\n
    6. Changes to our Privacy Policy
    If we decide to change our privacy policy, we will post those changes on this page. This policy was last modified on 23/04/2024.
    EOD
    ],
];

$topicId = $_GET['topic_id'] ?? 0; 
$topic = $helpTopics[$topicId];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail - <?= htmlspecialchars($topic['title']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<form>
    <button type="button" onclick="goBack()">Go Back</button>
    <div class="detail-container">
        <h1><?= htmlspecialchars($topic['title']); ?></h1>
        <p><?= nl2br($topic['fullDescription']); ?></p>
    </div>
</form>
<script>
    function goBack() {
        window.history.back();
    }
</script>
</body>
</html>
