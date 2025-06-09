<?php
session_start();
include 'db_connect.php';

// Ensure landlord is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    die("Access denied! Only landlords can add listings.");
}

$error = "";
$success = "";

// 🌍 Geocoding function
function geocodeLocation($location) {
    $encodedLocation = urlencode($location);
    $url = "https://nominatim.openstreetmap.org/search?q={$encodedLocation}&format=json&limit=1";

    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: ThikaRealEstate/1.0\r\n"
        ]
    ];

    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) return null;

    $data = json_decode($response, true);
    if (!empty($data[0])) {
        return [
            'lat' => $data[0]['lat'],
            'lon' => $data[0]['lon']
        ];
    }

    return null;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $location = trim($_POST['location']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $type = trim($_POST['type']);
    $bedrooms = trim($_POST['bedrooms']);
    $bathrooms = trim($_POST['bathrooms']);
    $landlord_id = $_SESSION['user_id'];

    if (empty($title) || empty($location) || empty($price) || empty($description) || empty($type) || empty($bedrooms) || empty($bathrooms)) {
        $error = "All fields are required!";
    } else {
        $uploadDir = "uploads/";
        $imagePaths = [];

        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['size'][$key] > 0) {
                    $fileName = $uploadDir . basename($_FILES['images']['name'][$key]);
                    if (move_uploaded_file($tmp_name, $fileName)) {
                        $imagePaths[] = $fileName;
                    }
                }
            }
        }

        $images = !empty($imagePaths) ? implode(",", $imagePaths) : "";

        // 🌍 Get lat/lng using OpenStreetMap + force float cast
        $geo = geocodeLocation($location);
        file_put_contents("geocode_log.txt", json_encode($geo, JSON_PRETTY_PRINT)); // Optional: Debug log
        $latitude = $geo ? (float)$geo['lat'] : null;
        $longitude = $geo ? (float)$geo['lon'] : null;

        $stmt = $conn->prepare("INSERT INTO listings 
            (title, location, price, description, type, bedrooms, bathrooms, images, landlord_id, latitude, longitude, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");

        $stmt->bind_param("ssdsssssidd", $title, $location, $price, $description, $type, $bedrooms, $bathrooms, $images, $landlord_id, $latitude, $longitude);

        if ($stmt->execute()) {
            $success = "Property listed successfully! Awaiting approval.";
        } else {
            $error = "Failed to post listing.";
        }

        $stmt->close();
    }
}
?>
