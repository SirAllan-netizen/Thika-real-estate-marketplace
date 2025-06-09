<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit a review.");
}

$user_id = $_SESSION['user_id'];
$listing_id = (int)$_POST['listing_id'];
$rating = (int)$_POST['rating'];
$review = trim($_POST['review']);

// Validate input
if ($rating < 1 || $rating > 5 || empty($review)) {
    die("Invalid input.");
}

// Optional: prevent duplicate reviews by same user for the same listing
$check = $conn->prepare("SELECT id FROM reviews WHERE user_id = ? AND listing_id = ?");
$check->bind_param("ii", $user_id, $listing_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    die("You have already submitted a review for this property.");
}
$check->close();

// Insert review
$stmt = $conn->prepare("INSERT INTO reviews (user_id, listing_id, rating, review) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiis", $user_id, $listing_id, $rating, $review);
$stmt->execute();
$stmt->close();

header("Location: property_details.php?id=" . $listing_id);
exit;
