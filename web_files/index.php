<?php 
include("navbar.php");
include("user_check.php");

$db->busyTimeout(5000);

$user_type = $_SESSION['user_type'] ?? 'Tenant';

// Handle both post and delete requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete']) && $user_type === 'Landlord') {
        // Handling deletion of an announcement
        $announcement_id = $db->escapeString($_POST['delete']);
        $db->exec('BEGIN');
        $sql = "DELETE FROM Announcements WHERE announcement_id = '$announcement_id'";
        if (!$db->exec($sql)) {
            echo "Error in database operation: " . $db->lastErrorMsg();
            $db->exec('ROLLBACK');
        } else {
            $db->exec('COMMIT');
            header("Location: ".$_SERVER['PHP_SELF']); // Redirect to refresh the page and avoid form resubmission issues
            exit();
        }
    } else {
        
        $title = $db->escapeString($_POST['title']);
        $content = $db->escapeString($_POST['content']);
        $user_id = $_SESSION['user_id'];

        $db->exec('BEGIN');
        $sql = "INSERT INTO Announcements (user_id, title, content, post_date) VALUES ('$user_id', '$title', '$content', datetime('now'))";
        if (!$db->exec($sql)) {
            echo "Error in database operation: " . $db->lastErrorMsg();
            $db->exec('ROLLBACK');
        } else {
            $db->exec('COMMIT');
        }
    }
}

$result = $db->query("SELECT a.announcement_id, a.title, a.content, a.post_date, u.user_fname, u.user_lname FROM Announcements a JOIN Users u ON a.user_id = u.user_id ORDER BY a.post_date DESC");
$announcements = [];
if ($result) {
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $announcements[] = $row;
    }
} else {
    echo "Failed to fetch announcements: " . $db->lastErrorMsg();
}

$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php if ($user_type === 'Landlord'): ?>
    <form action="" method="post">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea>
        <button type="submit">Post Announcement</button>
    </form>
<?php endif; ?>

<section>
    <?php foreach ($announcements as $announcement): ?>
        <article>
            <h2><?= htmlspecialchars($announcement['title']) ?></h2>
            <p><?= nl2br(htmlspecialchars($announcement['content'])) ?></p>
            <p>Posted by <?= htmlspecialchars($announcement['user_fname']) . ' ' . htmlspecialchars($announcement['user_lname']) ?> on <?= htmlspecialchars($announcement['post_date']) ?></p>
            <?php if ($user_type === 'Landlord'): ?>
                <form action="" method="post" class="delete-form">
                    <input type="hidden" name="delete" value="<?= $announcement['announcement_id'] ?>">
                    <button type="submit" class="delete-button">Delete</button>
                </form>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</section>

</body>
</html>
