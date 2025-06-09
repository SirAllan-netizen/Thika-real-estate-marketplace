<?php
include 'db_connect.php'; // Ensure this connects to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Secure password
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $agency_name = $_POST['agency_name'];
    $license_number = $_POST['license_number'];

    // Step 1: Insert into `users` table with role = 'agent'
    $sql = "INSERT INTO users (username, password, email, phone, role) VALUES (?, ?, ?, ?, 'agent')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $password, $email, $phone);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id; // Get the new agent's user ID

        // Step 2: Insert into `agents` table (Pending verification)
        $sql2 = "INSERT INTO agents (user_id, agency_name, license_number, phone, verified) VALUES (?, ?, ?, ?, 'pending')";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("isss", $user_id, $agency_name, $license_number, $phone);

        if ($stmt2->execute()) {
            echo "<script>alert('Registration successful! Waiting for admin approval.'); window.location='login.php';</script>";
        } else {
            echo "Error: " . $stmt2->error;
        }
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 w-96">
        <h2 class="text-2xl font-bold text-center mb-4">Agent Registration</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" class="w-full px-3 py-2 border rounded mb-2" required>
            <input type="password" name="password" placeholder="Password" class="w-full px-3 py-2 border rounded mb-2" required>
            <input type="email" name="email" placeholder="Email" class="w-full px-3 py-2 border rounded mb-2" required>
            <input type="text" name="phone" placeholder="Phone Number" class="w-full px-3 py-2 border rounded mb-2" required>
            <input type="text" name="agency_name" placeholder="Agency Name" class="w-full px-3 py-2 border rounded mb-2">
            <input type="text" name="license_number" placeholder="License Number" class="w-full px-3 py-2 border rounded mb-2" required>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-700">Register</button>
        </form>
    </div>
</body>
</html>
