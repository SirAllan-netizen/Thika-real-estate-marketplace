<?php
session_start();
require 'db_connect.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Make Payment</title>
</head>
<body>
    <h2>Mpesa Payment</h2>
    <form action="process_payment.php" method="POST">
        <label>Enter Amount (in KES):</label><br>
        <input type="number" name="amount" required><br><br>

        <label>Enter Phone Number (2547XXXXXXXX):</label><br>
        <input type="text" name="phone" placeholder="2547XXXXXXXX" required><br><br>

        <input type="hidden" name="payment_method" value="Mpesa">
        <input type="submit" value="Pay Now">
    </form>
</body>
</html>
