<?php
include ("navbar.php");
include("user_check.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $request_id = $_POST['request_id'] ?? '';
    $status = $_POST['status'] ?? 'Pending';

    $query = "UPDATE MaintenanceRequests SET request_status = :status WHERE request_id = :request_id";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':status', $status, SQLITE3_TEXT);
    $stmt->bindValue(':request_id', $request_id, SQLITE3_INTEGER);
    $stmt->execute();

    header('Location: maintenance_landlord.php'); // Redirect back
    exit;
}
?>
