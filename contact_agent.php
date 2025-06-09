<?php
include 'db_connect.php';

// Check if landlord_id and property_id are provided
if (!isset($_GET['landlord_id']) || !isset($_GET['property_id'])) {
    die("Invalid request. Please provide valid landlord and property IDs.");
}

$landlord_id = (int)$_GET['landlord_id'];
$property_id = (int)$_GET['property_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Agent</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4">Contact Agent</h2>
            
            <form action="send_message.php" method="POST" class="space-y-4">
                <input type="hidden" name="landlord_id" value="<?php echo $landlord_id; ?>">
                <input type="hidden" name="property_id" value="<?php echo $property_id; ?>">

                <div>
                    <label class="block mb-1">Your Name:</label>
                    <input type="text" name="sender_name" class="w-full p-2 border border-gray-300 rounded" required>
                </div>
                
                <div>
                    <label class="block mb-1">Your Email:</label>
                    <input type="email" name="sender_email" class="w-full p-2 border border-gray-300 rounded" required>
                </div>

                <div>
                    <label class="block mb-1">Message:</label>
                    <textarea name="message" rows="5" class="w-full p-2 border border-gray-300 rounded" required></textarea>
                </div>

                <button type="submit" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Send Message</button>
            </form>
        </div>
    </div>
</body>
</html>
