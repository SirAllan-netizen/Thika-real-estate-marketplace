<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    die("Access denied!");
}

$landlord_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    $query = "UPDATE landlords SET name = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $name, $email, $landlord_id);
    $stmt->execute();
    $stmt->close();
}

$result = $conn->query("SELECT * FROM landlords WHERE id = $landlord_id");
$landlord = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Settings</title>
</head>
<body>
    <h1>Settings</h1>
    <form method="POST">
        <label>Name:</label><br>
        <input type="text" name="name" value="<?= $landlord['name']; ?>"><br><br>
        
        <label>Email:</label><br>
        <input type="email" name="email" value="<?= $landlord['email']; ?>"><br><br>
        
        <button type="submit">Update</button>
    </form>
</body>
</html>
