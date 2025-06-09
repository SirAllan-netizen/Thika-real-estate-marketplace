<?php
session_start();
include 'db_connect.php';

$listing_id = $_GET['listing_id'] ?? null;

if (!$listing_id) {
    die("Invalid listing ID.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mpesa_code = trim($_POST['mpesa_code']);
    
    // Update the listing as premium if the payment code is valid
    $stmt = $conn->prepare("UPDATE listings SET premium_status = 'premium', mpesa_code = ? WHERE id = ?");
    $stmt->bind_param("si", $mpesa_code, $listing_id);
    
    if ($stmt->execute()) {
        echo "Listing upgraded to Premium successfully!";
    } else {
        echo "Failed to upgrade listing.";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Enter Mpesa Code</title>
</head>
<body>
    <h1>Enter Mpesa Code</h1>
    <form method="POST">
        <label>Mpesa Code:</label><br>
        <input type="text" name="mpesa_code" required><br><br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
