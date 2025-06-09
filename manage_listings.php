<?php
session_start();
include 'db_connect.php';

// Ensure only admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied! Only admins can approve listings.");
}

// Fetch all pending listings
$sql = "SELECT listings.id, listings.title, listings.location, listings.price, listings.description, listings.images, users.username 
        FROM listings 
        JOIN users ON listings.landlord_id = users.id 
        WHERE listings.status = 'pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Listings - Thika Real Estate</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-6 py-10">
        <h2 class="text-3xl font-bold mb-6">Manage Pending Listings</h2>

        <?php if ($result->num_rows > 0) { ?>
            <table class="w-full border-collapse bg-white shadow-md rounded-lg">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-4">Title</th>
                        <th class="p-4">Location</th>
                        <th class="p-4">Price (KES)</th>
                        <th class="p-4">Landlord</th>
                        <th class="p-4">Images</th>
                        <th class="p-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr class="border-t">
                            <td class="p-4"><?= $row['title']; ?></td>
                            <td class="p-4"><?= $row['location']; ?></td>
                            <td class="p-4"><?= number_format($row['price']); ?></td>
                            <td class="p-4"><?= $row['username']; ?></td>
                            <td class="p-4">
                                <?php 
                                    $images = explode(",", $row['images']);
                                    foreach ($images as $image) {
                                        echo "<img src='$image' class='w-20 h-20 object-cover rounded-md mr-2 inline-block'>";
                                    }
                                ?>
                            </td>
                            <td class="p-4">
                                <form action="approve_listing.php" method="POST">
                                    <input type="hidden" name="listing_id" value="<?= $row['id']; ?>">
                                    <button type="submit" name="action" value="approved" class="bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Approve</button>
                                    <button type="submit" name="action" value="rejected" class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700 ml-2">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="text-center text-gray-600 text-lg">No pending listings at the moment.</p>
        <?php } ?>
    </div>
</body>
</html>
