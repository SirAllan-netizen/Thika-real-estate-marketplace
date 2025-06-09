<?php
include 'db_connect.php';

header('Content-Type: application/json');

// Fetch filter values from URL parameters
$minPrice = isset($_GET['minPrice']) && is_numeric($_GET['minPrice']) ? (int)$_GET['minPrice'] : 0;
$maxPrice = isset($_GET['maxPrice']) && is_numeric($_GET['maxPrice']) ? (int)$_GET['maxPrice'] : 100000000;
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$listingType = isset($_GET['listingType']) ? trim($_GET['listingType']) : '';

// Prepare the SQL query with filters
$sql = "SELECT * FROM listings 
        WHERE status = 'approved' 
        AND price BETWEEN ? AND ?";

$params = [$minPrice, $maxPrice];
$types = "ii";

// Location Filter
if (!empty($location)) {
    $sql .= " AND location LIKE ?";
    $params[] = "%$location%";
    $types .= "s";
}

// Type Filter
if (!empty($type)) {
    $sql .= " AND type = ?";
    $params[] = $type;
    $types .= "s";
}

// Listing Type Filter (Premium or Basic)
if (!empty($listingType)) {
    if ($listingType === 'premium') {
        $sql .= " AND premium_status = 'premium'";
    } elseif ($listingType === 'basic') {
        $sql .= " AND premium_status = 'basic'";
    }
}

// Order by Premium Listings First, then Latest Listings
$sql .= " ORDER BY premium_status DESC, id DESC";

// Prepare the statement
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "Failed to prepare SQL statement."]);
    exit();
}

// Bind parameters
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$properties = [];

while ($row = $result->fetch_assoc()) {
    $row['images'] = explode(",", $row['images']); // Convert image string to array
    $properties[] = $row;
}

// Return properties as JSON
echo json_encode($properties);
file_put_contents("log_output.json", json_encode($properties, JSON_PRETTY_PRINT));


$stmt->close();
$conn->close();
