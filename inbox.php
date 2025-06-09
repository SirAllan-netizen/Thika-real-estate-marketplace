<?php
session_start();
include 'db_connect.php';

// Check if landlord is logged in
if (!isset($_SESSION['landlord_id'])) {
    echo "<script>alert('Please log in as a landlord to view messages.'); window.location.href = 'login.php';</script>";
    exit;
}

$landlord_id = $_SESSION['landlord_id'];

// Fetch messages for properties owned by the landlord
$sql = "SELECT m.id, m.name, m.email, m.message, m.created_at, p.title 
        FROM messages m
        JOIN properties p ON m.property_id = p.id
        WHERE p.landlord_id = ?
        ORDER BY m.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $landlord_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox - Messages</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-10 px-6">
        <h1 class="text-3xl font-bold">Inbox</h1>
        <div class="bg-white p-6 rounded-lg shadow-lg mt-6">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2 border">Property</th>
                        <th class="p-2 border">Sender</th>
                        <th class="p-2 border">Email</th>
                        <th class="p-2 border">Message</th>
                        <th class="p-2 border">Date</th>
                        <th class="p-2 border">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr class="border-b">
                            <td class="p-2 border"> <?php echo htmlspecialchars($row['title']); ?> </td>
                            <td class="p-2 border"> <?php echo htmlspecialchars($row['name']); ?> </td>
                            <td class="p-2 border"> <?php echo htmlspecialchars($row['email']); ?> </td>
                            <td class="p-2 border"> <?php echo htmlspecialchars($row['message']); ?> </td>
                            <td class="p-2 border"> <?php echo htmlspecialchars($row['created_at']); ?> </td>
                            <td class="p-2 border">
                                <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Reply</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <a href="landlord_dashboard.php" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Back to Dashboard</a>
    </div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
