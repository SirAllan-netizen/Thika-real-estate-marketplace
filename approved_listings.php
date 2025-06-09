<?php
session_start();
include 'db_connect.php';

// Ensure only admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied! Only admins can view approved listings.");
}

// Fetch Approved Listings
$query = "SELECT * FROM listings WHERE status = 'approved'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Listings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-blue-800 py-4">
        <div class="container mx-auto flex justify-between items-center px-6">
            <h1 class="text-2xl font-bold text-white">Admin Dashboard</h1>
            <ul class="flex space-x-6 text-white font-semibold">
                <li><a href="admin_dashboard.php" class="hover:underline">Pending Listings</a></li>
                <li><a href="logout.php" class="hover:underline">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-10 max-w-4xl bg-white shadow-md p-6 rounded-lg">
        <h2 class="text-2xl font-bold text-center mb-6">Approved Listings</h2>

        <?php while ($listing = mysqli_fetch_assoc($result)): ?>
            <div class="border p-4 mb-4 bg-gray-50 rounded shadow-md">
                <h3 class="text-lg font-semibold"><?= htmlspecialchars($listing['title']) ?></h3>
                <p><strong>Location:</strong> <?= htmlspecialchars($listing['location']) ?></p>
                <p><strong>Price:</strong> KES <?= number_format($listing['price']) ?></p>
                <p><strong>Description:</strong> <?= htmlspecialchars($listing['description']) ?></p>

                <!-- Display Images -->
                <?php
                $images = explode(',', $listing['images']);
                foreach ($images as $image): ?>
                    <img src="<?= htmlspecialchars($image) ?>" alt="Listing Image" class="mt-2 w-32 h-32 object-cover rounded">
                <?php endforeach; ?>
            </div>
        <?php endwhile; ?>

        <?php if (mysqli_num_rows($result) === 0): ?>
            <p class="text-center text-gray-500">No approved listings yet.</p>
        <?php endif; ?>

    </div>

</body>
</html>
