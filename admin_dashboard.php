<?php
session_start();
include 'db_connect.php';

// Ensure only admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied! Only admins can access this page.");
}

// Fetch total listings
$totalListingsQuery = "SELECT COUNT(*) AS total FROM listings";
$totalListingsResult = $conn->query($totalListingsQuery);
$totalListings = $totalListingsResult->fetch_assoc()['total'];

// Fetch total pending agents
$pendingAgentsQuery = "SELECT COUNT(*) AS total FROM agents WHERE verified = 'pending'";
$pendingAgentsResult = $conn->query($pendingAgentsQuery);
$pendingAgents = $pendingAgentsResult->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-6 py-10">
        <h2 class="text-3xl font-bold mb-6">Admin Dashboard</h2>

        <!-- Manage Listings Button -->
        <div class="mb-6">
            <a href="manage_listings.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Manage Pending Listings
            </a>
        </div>

        <!-- New: Manage Agents Button -->
        <div class="mb-6">
            <a href="manage_agents.php" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                Manage Pending Agents (<?= $pendingAgents ?>)
            </a>
        </div>

        <h3 class="text-2xl font-bold mt-8">System Stats</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-blue-500 text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-bold">Total Listings</h3>
                <p class="text-4xl"><?= $totalListings ?></p>
            </div>
            <div class="bg-yellow-500 text-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-bold">Pending Agents</h3>
                <p class="text-4xl"><?= $pendingAgents ?></p>
            </div>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
