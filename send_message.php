<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $landlord_id = intval($_POST['landlord_id']);
    $sender_name = $_POST['sender_name'];
    $sender_email = $_POST['sender_email'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO messages (landlord_id, sender_name, sender_email, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $landlord_id, $sender_name, $sender_email, $message);

    if ($stmt->execute()) {
        echo "Message sent successfully!";
    } else {
        echo "Failed to send message.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contact Landlord</title>
</head>
<body>
    <form action="send_message.php" method="POST">
        <input type="hidden" name="landlord_id" value="1"> <!-- Replace with actual landlord ID -->
        
        <label>Name:</label><br>
        <input type="text" name="sender_name" required><br><br>
        
        <label>Email:</label><br>
        <input type="email" name="sender_email" required><br><br>
        
        <label>Message:</label><br>
        <textarea name="message" required></textarea><br><br>
        
        <button type="submit">Send Message</button>
    </form>
</body>
</html>
