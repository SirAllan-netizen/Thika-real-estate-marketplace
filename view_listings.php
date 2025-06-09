<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    die("Access denied!");
}

$landlord_id = $_SESSION['user_id'];
$query = "SELECT * FROM listings WHERE landlord_id = $landlord_id ORDER BY premium_status DESC";
$result = $conn->query($query);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $listing_id = $_POST['listing_id'];
    $premium_status = 'pending_payment';  // Waiting for Mpesa code verification
    
    $stmt = $conn->prepare("UPDATE listings SET premium_status = ? WHERE id = ?");
    $stmt->bind_param("si", $premium_status, $listing_id);
    $stmt->execute();
    $stmt->close();

    header("Location: payment_page.php?listing_id=$listing_id");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Listings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">

<div class="container mx-auto px-6 py-10">
    <h1 class="text-3xl font-bold mb-6">My Listings</h1>
    
    <table class="table-auto w-full bg-white shadow-md rounded mb-4">
        <thead>
            <tr>
                <th>Title</th>
                <th>Location</th>
                <th>Price (KES)</th>
                <th>Status</th>
                <th>Premium Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['title']; ?></td>
                    <td><?= $row['location']; ?></td>
                    <td><?= $row['price']; ?></td>
                    <td><?= $row['status']; ?></td>
                    <td><?= $row['premium_status']; ?></td>
                    <td>
                        <?php if ($row['premium_status'] === 'basic'): ?>
                            <form method="POST" action="view_listings.php">
                                <input type="hidden" name="listing_id" value="<?= $row['id']; ?>">
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Upgrade to Premium</button>
                            </form>
                        <?php else: ?>
                            <span class="text-green-500">Premium</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
