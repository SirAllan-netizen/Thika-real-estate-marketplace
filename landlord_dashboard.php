<?php 
session_start();
include 'db_connect.php';  // Database connection file
include 'access_token.php'; // Include the access token function

// Ensure landlord is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    die("Access denied! Only landlords can access this dashboard.");
}

$landlord_id = $_SESSION['user_id'];

// Fetch total properties
$result = $conn->query("SELECT COUNT(*) AS total FROM listings WHERE landlord_id = $landlord_id");
$totalProperties = $result->fetch_assoc()['total'];

// Fetch premium listings
$result = $conn->query("SELECT COUNT(*) AS total FROM listings WHERE landlord_id = $landlord_id AND premium_status = 'premium'");
$totalPremiumListings = $result->fetch_assoc()['total'];

// Fetch pending listings
$result = $conn->query("SELECT COUNT(*) AS total FROM listings WHERE landlord_id = $landlord_id AND status = 'pending'");
$totalPendingListings = $result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landlord Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">

<div class="container mx-auto px-6 py-10">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Landlord Dashboard</h1>
        <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded">Logout</a>
    </div>

    <!-- Dashboard Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded shadow text-center">
            <h2 class="text-xl font-bold mb-2">Total Properties</h2>
            <p class="text-4xl text-blue-500"><?= $totalProperties ?></p>
        </div>
        <div class="bg-white p-6 rounded shadow text-center">
            <h2 class="text-xl font-bold mb-2">Premium Listings</h2>
            <p class="text-4xl text-green-500"><?= $totalPremiumListings ?></p>
        </div>
        <div class="bg-white p-6 rounded shadow text-center">
            <h2 class="text-xl font-bold mb-2">Pending Listings</h2>
            <p class="text-4xl text-yellow-500"><?= $totalPendingListings ?></p>
        </div>
    </div>

    <!-- Dashboard Navigation -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <a href="post_listing.php" class="bg-blue-500 text-white p-6 rounded shadow hover:bg-blue-600 transition">Post New Listing</a>
        <a href="view_listings.php" class="bg-green-500 text-white p-6 rounded shadow hover:bg-green-600 transition">View Listings</a>
        <a href="messages.php" class="bg-purple-500 text-white p-6 rounded shadow hover:bg-purple-600 transition">Messages</a>
        <a href="notifications.php" class="bg-yellow-500 text-white p-6 rounded shadow hover:bg-yellow-600 transition">Notifications</a>
        <a href="settings.php" class="bg-pink-500 text-white p-6 rounded shadow hover:bg-pink-600 transition">Settings</a>
        <a href="premium_listings.php" class="bg-indigo-500 text-white p-6 rounded shadow hover:bg-indigo-600 transition">Premium Listings</a>
    </div>

    <!-- Your Listings Table -->
    <h2 class="text-2xl font-bold mt-8 mb-4">Your Listings</h2>
    <table class="min-w-full table-auto bg-white shadow-md rounded">
        <thead>
            <tr>
                <th class="px-6 py-3 border-b">Title</th>
                <th class="px-6 py-3 border-b">Location</th>
                <th class="px-6 py-3 border-b">Price</th>
                <th class="px-6 py-3 border-b">Premium Status</th>
                <th class="px-6 py-3 border-b">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $listingQuery = "SELECT * FROM listings WHERE landlord_id = $landlord_id ORDER BY premium_status DESC";
            $listingResult = $conn->query($listingQuery);
            while ($listing = $listingResult->fetch_assoc()):
            ?>
                <tr>
                    <td class="px-6 py-3 border-b"><?= $listing['title'] ?></td>
                    <td class="px-6 py-3 border-b"><?= $listing['location'] ?></td>
                    <td class="px-6 py-3 border-b"><?= $listing['price'] ?></td>
                    <td class="px-6 py-3 border-b"><?= $listing['premium_status'] == 'premium' ? 'Premium' : 'Normal' ?></td>
                    <td class="px-6 py-3 border-b">
                        <?php if ($listing['premium_status'] != 'premium'): ?>
                            <form action="stk_push.php" method="POST">
                                <input type="hidden" name="listing_id" value="<?= $listing['id'] ?>">
                                <label for="phone_number">Enter Mpesa Phone Number:</label>
                                <input type="text" name="phone_number" placeholder="2547XXXXXXXX" required class="p-2 border rounded w-full"><br><br>
                                <button type="submit" class="bg-green-500 text-white p-2 rounded">Upgrade to Premium</button>
                            </form>
                        <?php else: ?>
                            <span class="text-green-600">Already Premium</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>

</body>
</html>
