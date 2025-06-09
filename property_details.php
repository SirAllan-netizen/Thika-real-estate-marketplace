<?php
session_start();
include 'db_connect.php';

if (!isset($_GET['id'])) {
    die("Invalid property ID.");
}

$property_id = (int)$_GET['id'];
$sql = "SELECT * FROM listings WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $property_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Property not found.");
}

$property = $result->fetch_assoc();

// Fetch reviews
$reviewStmt = $conn->prepare("SELECT r.rating, r.review, r.created_at, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.listing_id = ?");
$reviewStmt->bind_param("i", $property_id);
$reviewStmt->execute();
$reviews = $reviewStmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($property['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.0" defer></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <!-- Property Information Card -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h1 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($property['title']); ?></h1>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($property['location']); ?></p>
            <p><strong>Price:</strong> KES <?php echo number_format($property['price'], 2); ?></p>
            <p><strong>Type:</strong> <?php echo htmlspecialchars($property['type']); ?></p>
            <p><strong>Description:</strong></p>
            <div class="mt-2 text-gray-700 leading-relaxed">
                <?php echo nl2br(htmlspecialchars($property['description'])); ?>
            </div>
            <!-- Contact Agent/Landlord Button -->
            <div class="mt-6">
                <a href="contact_agent.php?landlord_id=<?php echo $property['landlord_id']; ?>&property_id=<?php echo $property_id; ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Contact Agent/Landlord</a>
            </div>
        </div>

        <!-- Image Gallery -->
        <?php if (!empty($property['images'])): ?>
            <div class="bg-white p-6 rounded-lg shadow-lg mt-6">
                <h2 class="text-2xl font-bold mb-4">Gallery</h2>
                <div class="grid grid-cols-3 gap-4">
                    <?php 
                    $images = explode(",", $property['images']);
                    foreach ($images as $image): ?>
                        <div class="relative">
                            <img src="<?php echo htmlspecialchars($image); ?>" alt="Property Image" class="w-full h-48 object-cover rounded-lg cursor-pointer" onclick="openModal('<?php echo htmlspecialchars($image); ?>')">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Review Form -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="bg-white p-6 rounded-lg shadow-lg mt-8">
                <h3 class="text-xl font-semibold mb-3">Leave a Review</h3>
                <form method="POST" action="submit_review.php">
                    <input type="hidden" name="listing_id" value="<?= $property_id ?>">
                    
                    <label for="rating" class="block font-medium mb-1">Rating:</label>
                    <select name="rating" required class="w-full border p-2 rounded mb-4">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
                        <?php endfor; ?>
                    </select>

                    <label for="review" class="block font-medium mb-1">Review:</label>
                    <textarea name="review" rows="4" required class="w-full border rounded p-2 mb-4" placeholder="Your thoughts about this property..."></textarea>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit Review</button>
                </form>
            </div>
        <?php else: ?>
            <p class="mt-6 text-gray-600">Please <a href="login.html" class="text-blue-600 underline">log in</a> to leave a review.</p>
        <?php endif; ?>

        <!-- Display Reviews -->
        <div class="bg-white p-6 rounded-lg shadow-lg mt-10">
            <h3 class="text-xl font-bold mb-4">User Reviews</h3>
            <?php if ($reviews->num_rows > 0): ?>
                <?php while ($rev = $reviews->fetch_assoc()): ?>
                    <div class="border-b border-gray-200 pb-4 mb-4">
                        <div class="text-yellow-500 mb-1">
                            <?= str_repeat('★', $rev['rating']) . str_repeat('☆', 5 - $rev['rating']) ?>
                        </div>
                        <p class="text-sm text-gray-800"><?= htmlspecialchars($rev['review']) ?></p>
                        <small class="text-gray-500">By <?= htmlspecialchars($rev['username']) ?> on <?= date('F j, Y', strtotime($rev['created_at'])) ?></small>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-gray-500">No reviews yet for this property.</p>
            <?php endif; ?>
        </div>

        <!-- Image Modal -->
        <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden">
            <div class="relative bg-white p-4 rounded-lg">
                <span class="absolute top-0 right-0 text-xl cursor-pointer text-gray-500" onclick="closeModal()">&times;</span>
                <img id="modalImage" src="" alt="Property Image" class="max-w-full max-h-screen rounded-lg">
            </div>
        </div>
    </div>

    <script>
        function openModal(imageUrl) {
            document.getElementById('modalImage').src = imageUrl;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.getElementById('modalImage').src = "";
        }
    </script>
</body>
</html>
