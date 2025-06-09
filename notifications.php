<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    die("Access denied!");
}

$landlord_id = $_SESSION['user_id'];

$query = "SELECT * FROM notifications WHERE user_id = $landlord_id ORDER BY timestamp DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
</head>
<body>
    <h1>Notifications</h1>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div>
            <strong><?= $row['title']; ?></strong>
            <p><?= $row['message']; ?></p>
            <small><?= $row['timestamp']; ?></small>
            <hr>
        </div>
    <?php endwhile; ?>
</body>
</html>
