<?php
include 'db_connect.php';

$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$location_filter = isset($_GET['location']) ? $_GET['location'] : '';
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';
$price_filter = isset($_GET['price']) ? $_GET['price'] : '';

$conditions = [];
$params = [];
$types = '';

if (!empty($search_query)) {
    $conditions[] = "(title LIKE ? OR location LIKE ? OR description LIKE ?)";
    $like_query = "%{$search_query}%";
    $params[] = $like_query;
    $params[] = $like_query;
    $params[] = $like_query;
    $types .= 'sss';
    $page_heading = "Search results for: " . htmlspecialchars($search_query);
} elseif (!empty($location_filter)) {
    $conditions[] = "location = ?";
    $params[] = $location_filter;
    $types .= 's';
    $page_heading = htmlspecialchars($location_filter);
} else {
    $page_heading = "Available Listings";
}

if (!empty($type_filter)) {
    $conditions[] = "type = ?";
    $params[] = $type_filter;
    $types .= 's';
}

if (!empty($price_filter)) {
    switch ($price_filter) {
        case 'below-10000':
            $conditions[] = "price < 10000";
            break;
        case '10000-30000':
            $conditions[] = "price BETWEEN 10000 AND 30000";
            break;
        case '30000-50000':
            $conditions[] = "price BETWEEN 30000 AND 50000";
            break;
        case 'above-50000':
            $conditions[] = "price > 50000";
            break;
    }
}

$whereClause = $conditions ? ("WHERE " . implode(" AND ", $conditions)) : '';
$sql = "SELECT * FROM listings $whereClause ORDER BY id DESC";
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$results = $stmt->get_result();
$listings = $results->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Properties - Thika Real Estate</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; }
    .hover-card:hover { transform: scale(1.03); transition: 0.3s ease-in-out; }
  </style>
</head>
<body class="bg-gray-100">

<!-- Navbar -->
<nav class="bg-white shadow-md py-4 sticky top-0 z-50">
  <div class="container mx-auto flex justify-between items-center px-6">
    <h1 class="text-2xl font-bold text-blue-600">Thika Real Estate</h1>
    <a href="index.php" class="text-sm text-blue-600 hover:underline">Back to Home</a>
  </div>
</nav>

<!-- Search & Filters -->
<div class="container mx-auto mt-10 px-6">
  <form method="GET" action="properties.php" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <input type="text" name="search" placeholder="Search by title, location, or description..." value="<?= htmlspecialchars($search_query); ?>"
           class="col-span-2 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-300">

    <select name="type" class="px-4 py-2 border border-gray-300 rounded-md">
      <option value="">All Types</option>
      <option value="Bedsitter" <?= $type_filter == 'Bedsitter' ? 'selected' : '' ?>>Bedsitter</option>
      <option value="1 Bedroom" <?= $type_filter == '1 Bedroom' ? 'selected' : '' ?>>1 Bedroom</option>
      <option value="2 Bedroom" <?= $type_filter == '2 Bedroom' ? 'selected' : '' ?>>2 Bedroom</option>
      <option value="Apartment" <?= $type_filter == 'Apartment' ? 'selected' : '' ?>>Apartment</option>
    </select>

    <select name="price" class="px-4 py-2 border border-gray-300 rounded-md">
      <option value="">All Prices</option>
      <option value="below-10000" <?= $price_filter == 'below-10000' ? 'selected' : '' ?>>Below KES 10,000</option>
      <option value="10000-30000" <?= $price_filter == '10000-30000' ? 'selected' : '' ?>>KES 10,000 - 30,000</option>
      <option value="30000-50000" <?= $price_filter == '30000-50000' ? 'selected' : '' ?>>KES 30,000 - 50,000</option>
      <option value="above-50000" <?= $price_filter == 'above-50000' ? 'selected' : '' ?>>Above KES 50,000</option>
    </select>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Search</button>
  </form>
</div>

<!-- Listings -->
<div class="container mx-auto py-6 px-6">
  <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">
    <?= $page_heading ?>
  </h1>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php if (empty($listings)): ?>
      <p class="col-span-full text-center text-gray-600">No properties found.</p>
    <?php else: ?>
      <?php foreach ($listings as $property): ?>
        <?php
          $imageRaw = trim($property['images']);
          $imageFile = !empty($imageRaw) ? explode(',', $imageRaw)[0] : '';
          $imagePath = !empty($imageFile) ? "uploads/" . $imageFile : "uploads/default-placeholder.jpg";
        ?>
        <div class="hover-card bg-white p-4 rounded-lg shadow-md transition-transform">
          <img src="<?= $imagePath ?>" class="w-full h-48 object-cover rounded" alt="<?= htmlspecialchars($property['title']) ?>">
          <h2 class="text-xl font-semibold mt-2 text-gray-800"><?= htmlspecialchars($property['title']); ?></h2>
          <p class="text-gray-500 text-sm mb-1">Location: <?= htmlspecialchars($property['location']); ?></p>
          <p class="text-green-600 font-bold text-lg">KES <?= number_format($property['price']); ?></p>
          <a href="property_details.php?id=<?= urlencode($property['id']); ?>" class="mt-3 block bg-blue-600 text-white text-center py-2 rounded hover:bg-blue-700">
            View & Contact
          </a>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
