<?php
session_start();
include 'db_connect.php';

// Ensure only admins can approve/reject listings and agents
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied! Only admins can approve listings and agents.");
}

// Handle property listing approval
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['listing_id'], $_POST['action'])) {
        $listing_id = $_POST['listing_id'];
        $status = $_POST['action']; // 'approved' or 'rejected'

        if (!in_array($status, ['approved', 'rejected'])) {
            die("Invalid action.");
        }

        // Update listing status in the database
        $sql = "UPDATE listings SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $listing_id);
        $stmt->execute();

        header("Location: manage_listings.php");
        exit();
    }

    // Handle agent approval
    if (isset($_POST['agent_id'], $_POST['agent_action'])) {
        $agent_id = $_POST['agent_id'];
        $agent_status = $_POST['agent_action']; // 'approved' or 'rejected'

        if (!in_array($agent_status, ['approved', 'rejected'])) {
            die("Invalid action.");
        }

        // Update agent verification status
        $sql = "UPDATE agents SET verified = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $agent_status, $agent_id);
        $stmt->execute();

        header("Location: manage_agents.php");
        exit();
    }
}
?>
