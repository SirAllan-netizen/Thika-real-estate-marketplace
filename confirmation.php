<?php
session_start();
include 'db_connect.php';

$data = file_get_contents('php://input');
$transaction = json_decode($data);

if ($transaction->Body->stkCallback->ResultCode == 0) {
    $mpesa_code = $transaction->Body->stkCallback->CheckoutRequestID;

    $stmt = $conn->prepare("UPDATE listings SET payment_status = 'paid', premium_status = 'premium', mpesa_code = ? WHERE id = ?");
    $stmt->bind_param("si", $mpesa_code, $_SESSION['listing_id']);
    
    if ($stmt->execute()) {
        echo "Premium Listing successfully updated!";
        unset($_SESSION['listing_id']);
    } else {
        echo "Failed to update the listing.";
    }

    $stmt->close();
}
