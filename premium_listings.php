<?php
session_start();
include 'db_connect.php';

// Fetch premium listings from the database
$stmt = $conn->prepare("SELECT id, title, location, price, status, images FROM listings WHERE status = 'approved' ORDER BY id DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Listings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-6 py-10">
        <h1 class="text-4xl font-bold mb-6 text-blue-600">Premium Listings</h1>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($row = $result->fetch_assoc()): 
                    $images = explode(",", $row['images']);
                    $mainImage = !empty($images[0]) ? $images[0] : 'uploads/default.jpg';
                ?>
                    <div class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <img src="<?= $mainImage ?>" alt="Property Image" class="w-full h-48 object-cover">

                        <div class="p-4">
                            <h2 class="text-2xl font-bold"><?= htmlspecialchars($row['title']) ?></h2>
                            <p class="text-gray-600">Location: <?= htmlspecialchars($row['location']) ?></p>
                            <p class="text-gray-600">Price: KES <?= number_format($row['price'], 2) ?></p>
                            <p class="text-green-500 font-bold"><?= ucfirst($row['status']) ?></p>

                            <a href="property_details.php?id=<?= $row['id'] ?>" class="block mt-4 bg-blue-600 text-white text-center p-2 rounded hover:bg-blue-700 transition-colors">View Details</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="bg-red-500 text-white p-4 mb-6 rounded">No premium listings found.</div>
        <?php endif; ?>

        <?php $stmt->close(); ?>
    </div>
</body>
</html>
