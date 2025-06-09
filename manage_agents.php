<?php
session_start();
include 'db_connect.php';

// Ensure only admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied! Only admins can manage agents.");
}

// Fetch all pending agents
$query = "SELECT agents.id, users.username, agents.agency_name, agents.license_number, agents.phone 
          FROM agents 
          JOIN users ON agents.user_id = users.id 
          WHERE agents.verified = 'pending'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Agents</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-6 py-10">
        <h2 class="text-3xl font-bold mb-6">Pending Agent Approvals</h2>

        <table class="w-full bg-white shadow-md rounded-lg">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-4">Username</th>
                    <th class="p-4">Agency</th>
                    <th class="p-4">License Number</th>
                    <th class="p-4">Phone</th>
                    <th class="p-4">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr class="border-t">
                        <td class="p-4"><?= $row['username'] ?></td>
                        <td class="p-4"><?= $row['agency_name'] ?></td>
                        <td class="p-4"><?= $row['license_number'] ?></td>
                        <td class="p-4"><?= $row['phone'] ?></td>
                        <td class="p-4">
                            <form method="POST" action="approve_listing.php">
                                <input type="hidden" name="agent_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="agent_action" value="approved" class="bg-green-500 text-white px-4 py-2 rounded">Approve</button>
                                <button type="submit" name="agent_action" value="rejected" class="bg-red-500 text-white px-4 py-2 rounded">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>
