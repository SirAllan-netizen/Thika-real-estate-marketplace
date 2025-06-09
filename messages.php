<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    die("Access denied!");
}

$landlord_id = $_SESSION['user_id'];

// Handle reply submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $message_id = intval($_POST['message_id']);
    $reply = $_POST['reply'];

    $stmt = $conn->prepare("UPDATE messages SET reply = ? WHERE id = ? AND landlord_id = ?");
    $stmt->bind_param("sii", $reply, $message_id, $landlord_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch messages sent to this landlord
$query = "SELECT * FROM messages WHERE landlord_id = $landlord_id ORDER BY timestamp DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Messages</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-6 py-10">
        <h1 class="text-3xl font-bold mb-6">Messages</h1>
        
        <table class="table-auto w-full bg-white shadow-md rounded mb-4">
            <thead>
                <tr>
                    <th class="px-4 py-2">Sender Name</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Message</th>
                    <th class="px-4 py-2">Reply</th>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?= htmlspecialchars($row['sender_name']); ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($row['sender_email']); ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($row['message']); ?></td>
                        <td class="px-4 py-2">
                            <?= $row['reply'] ? htmlspecialchars($row['reply']) : 'No reply yet'; ?>
                        </td>
                        <td class="px-4 py-2"><?= $row['timestamp']; ?></td>
                        <td class="px-4 py-2">
                            <?php if (!$row['reply']): ?>
                                <form action="messages.php" method="POST" class="flex flex-col">
                                    <textarea name="reply" class="border p-2 mb-2" placeholder="Type your reply here..."></textarea>
                                    <input type="hidden" name="message_id" value="<?= $row['id']; ?>">
                                    <button type="submit" class="bg-blue-500 text-white p-2 rounded">Reply</button>
                                </form>
                            <?php else: ?>
                                <span class="text-green-500">Replied</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
