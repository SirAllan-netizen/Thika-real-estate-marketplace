<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: login.php?message=Please log in to post a listing.");
    exit();
}

// If logged in but not a landlord, deny access
if ($_SESSION['role'] !== 'landlord') {
    die("Access denied! Only landlords can post listings.");
}

// If landlord is logged in, redirect to post a listing page
header("Location: add_listing.html");
exit();
?>
