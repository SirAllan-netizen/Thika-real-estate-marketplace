<?php
$host = "localhost"; // XAMPP default
$user = "root"; // Default XAMPP MySQL user
$pass = ""; // Default is empty in XAMPP
$dbname = "thika_real_estate"; // Your database name

$conn = mysqli_connect($host, $user, $pass, $dbname);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>

