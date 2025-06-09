<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $property_id = intval($_POST['property_id']);
    $message = trim($_POST['message']);

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('You must be logged in to send a message.'); window.location.href = 'login.php';</script>";
        exit;
    }

    $sender_id = $_SESSION['user_id']; // The logged-in user sending the message

    // Fetch landlord ID (receiver) from the `listings` table
    $stmt = $conn->prepare("SELECT landlord_id FROM listings WHERE id = ?");
    $stmt->bind_param("i", $property_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $property = $result->fetch_assoc();
    
    if (!$property) {
        echo "<script>alert('Property not found.'); window.history.back();</script>";
        exit;
    }

    $receiver_id = $property['landlord_id']; // The landlord who owns the property
    $stmt->close();

    // Insert message into the database
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, property_id, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $sender_id, $receiver_id, $property_id, $message);

    if ($stmt->execute()) {
        echo "<script>alert('Message sent successfully!'); window.location.href = 'property_details.php?id=$property_id';</script>";
    } else {
        echo "<script>alert('Failed to send message. Try again.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
