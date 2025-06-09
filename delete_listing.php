<?php
session_start();
include 'db_connect.php';

// Ensure landlord is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    die("Access denied! Only landlords can delete listings.");
}

$landlord_id = $_SESSION['user_id'];
$listing_id = $_GET['id'] ?? 0;

// Verify if the listing belongs to the landlord
$sql = "SELECT images FROM listings WHERE id = ? AND landlord_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $listing_id, $landlord_id);
$stmt->execute();
$result = $stmt->get_result();
$listing = $result->fetch_assoc();

if (!$listing) {
    die("Unauthorized access or listing not found.");
}

// Delete images from server
$imagePaths = explode(",", $listing['images']);
foreach ($imagePaths as $image) {
    if (file_exists($image)) {
        unlink($image);
    }
}

// Delete listing from database
$deleteSql = "DELETE FROM listings WHERE id = ? AND landlord_id = ?";
$stmt = $conn->prepare($deleteSql);
$stmt->bind_param("ii", $listing_id, $landlord_id);
$stmt->execute();

header("Location: landlord_dashboard.php");
exit();
?>
