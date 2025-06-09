<?php
session_start();
include 'db_connect.php';

// Ensure landlord is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    die("Access denied! Only landlords can edit listings.");
}

$landlord_id = $_SESSION['user_id'];
$listing_id = $_GET['id'] ?? 0;

// Fetch listing data
$sql = "SELECT * FROM listings WHERE id = ? AND landlord_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $listing_id, $landlord_id);
$stmt->execute();
$result = $stmt->get_result();
$listing = $result->fetch_assoc();

if (!$listing) {
    die("Listing not found or unauthorized access.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $type = $_POST['type'];
    
    // Handle image uploads
    $imagePaths = explode(",", $listing['images']); // Keep existing images
    $uploadDir = "uploads/";
    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['images']['size'][$key] > 0) {
            $fileName = $uploadDir . basename($_FILES['images']['name'][$key]);
            if (move_uploaded_file($tmp_name, $fileName)) {
                $imagePaths[] = $fileName;
            }
        }
    }
    $images = implode(",", $imagePaths);

    // Update listing in database
    $updateSql = "UPDATE listings SET title=?, location=?, price=?, type=?, images=? WHERE id=? AND landlord_id=?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ssdssii", $title, $location, $price, $type, $images, $listing_id, $landlord_id);
    $stmt->execute();

    header("Location: landlord_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Listing</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-6 py-10">
        <h2 class="text-3xl font-bold mb-6">Edit Listing</h2>
        <form action="edit_listing.php?id=<?= $listing_id ?>" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
            <label>Title:</label>
            <input type="text" name="title" value="<?= $listing['title'] ?>" class="w-full p-2 border rounded mb-4" required>
            
            <label>Location:</label>
            <input type="text" name="location" value="<?= $listing['location'] ?>" class="w-full p-2 border rounded mb-4" required>
            
            <label>Price (KES):</label>
            <input type="number" name="price" value="<?= $listing['price'] ?>" class="w-full p-2 border rounded mb-4" required>
            
            <label>Type:</label>
            <select name="type" class="w-full p-2 border rounded mb-4">
                <option value="Apartment" <?= ($listing['type'] == "Apartment") ? "selected" : "" ?>>Apartment</option>
                <option value="Villa" <?= ($listing['type'] == "Villa") ? "selected" : "" ?>>Villa</option>
                <option value="House" <?= ($listing['type'] == "House") ? "selected" : "" ?>>House</option>
            </select>

            <label>Current Images:</label>
            <div class="flex gap-2">
                <?php foreach (explode(",", $listing['images']) as $img) : ?>
                    <img src="<?= $img ?>" class="w-20 h-20 object-cover rounded-md">
                <?php endforeach; ?>
            </div>

            <label>Upload New Images:</label>
            <input type="file" name="images[]" multiple class="w-full p-2 border rounded mb-4">

            <button type="submit" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Save Changes</button>
        </form>
    </div>
</body>
</html>
