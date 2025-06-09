<?php
include 'db_connect.php';

// Get search parameters
$location = isset($_GET['location']) ? $_GET['location'] : '';
$propertyType = isset($_GET['propertyType']) ? $_GET['propertyType'] : '';
$priceRange = isset($_GET['priceRange']) ? $_GET['priceRange'] : '';

// Start SQL query
$sql = "SELECT * FROM listings WHERE status = 'approved'";

// Apply filters
if (!empty($location)) {
    $sql .= " AND location LIKE '%$location%'";
}

if (!empty($propertyType)) {
    $sql .= " AND type = '$propertyType'";
}

if (!empty($priceRange)) {
    if ($priceRange == "below50") {
        $sql .= " AND price < 50000";
    } elseif ($priceRange == "50to100") {
        $sql .= " AND price BETWEEN 50000 AND 100000";
    } elseif ($priceRange == "above100") {
        $sql .= " AND price > 100000";
    }
}

$result = $conn->query($sql);

$properties = [];

while ($row = $result->fetch_assoc()) {
    $properties[] = $row;
}

// Return results as JSON
header('Content-Type: application/json');
echo json_encode($properties);
?>
