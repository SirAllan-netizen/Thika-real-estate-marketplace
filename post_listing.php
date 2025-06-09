<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $location = $_POST['location'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $listing_type = $_POST['listing_type']; // 'basic' or 'premium'
    $mpesa_code = isset($_POST['mpesa_code']) ? $_POST['mpesa_code'] : null;
    $landlord_id = $_SESSION['user_id'];

    $premium_status = 'basic'; // Default to basic

    if ($listing_type === 'premium') {
        if (empty($mpesa_code)) {
            die("Please enter an Mpesa code to proceed with the premium listing.");
        }
        $premium_status = 'premium'; // Set to 'premium' if selected
    }

    $imagePath = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES['image']['name']);
        $targetDirectory = "uploads/";
        $targetFile = $targetDirectory . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile;
        }
    }

    $stmt = $conn->prepare("INSERT INTO listings (title, location, type, price, description, premium_status, mpesa_code, landlord_id, image) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdsisss", $title, $location, $type, $price, $description, $premium_status, $mpesa_code, $landlord_id, $imagePath);

    if ($stmt->execute()) {
        echo "Listing added successfully!";
    } else {
        echo "Failed to add listing.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a New Listing</title>
</head>
<body>
    <h2>Post Your Property</h2>
    <form action="post_listing.php" method="POST" enctype="multipart/form-data">
        <label for="title">Property Title:</label>
        <input type="text" name="title" required><br><br>

        <label for="location">Location:</label>
        <input type="text" name="location" required><br><br>

        <label for="type">Property Type:</label>
        <select name="type" required>
            <option value="Apartment">Apartment</option>
            <option value="Villa">Villa</option>
            <option value="Townhouse">Townhouse</option>
        </select><br><br>

        <label for="price">Price (KES):</label>
        <input type="number" name="price" step="0.01" required><br><br>

        <label for="description">Description:</label>
        <textarea name="description" required></textarea><br><br>

        <label for="listing_type">Listing Type:</label>
        <select name="listing_type" required>
            <option value="basic">Basic (Free Listing)</option>
            <option value="premium">Premium (KES 500 - Pay via Mpesa)</option>
        </select><br><br>

        <label for="mpesa_code">Enter Mpesa Code (if Premium):</label>
        <input type="text" name="mpesa_code" placeholder="Enter Mpesa Code (only for premium listings)"><br><br>

        <label for="image">Upload Image:</label>
        <input type="file" name="image" accept="image/*"><br><br>

        <button type="submit">Post Listing</button>
    </form>
</body>
</html>
