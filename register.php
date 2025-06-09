<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Secure hashing
    $role = $_POST['role']; // 'tenant' or 'landlord'

    // Ensure role is either 'tenant' or 'landlord' only
    if (!in_array($role, ['tenant', 'landlord'])) {
        die("Invalid role selected.");
    }

    // Insert into users table
    $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! You can now login.'); window.location='login.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
