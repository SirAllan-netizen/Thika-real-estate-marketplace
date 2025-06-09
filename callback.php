<?php
include 'db_connect.php';  // Ensure your database connection file is included

// Get the raw POST data from Safaricom's server
$data = file_get_contents("php://input");

// Decode the JSON data
$decoded_data = json_decode($data, true);

// Logging the incoming data for debugging purposes
file_put_contents("mpesa_callback_log.txt", $data . "\n", FILE_APPEND);

// Check if the data was received properly
if ($decoded_data) {
    $resultCode = $decoded_data['Body']['stkCallback']['ResultCode'];
    $resultDesc = $decoded_data['Body']['stkCallback']['ResultDesc'];
    $checkoutRequestID = $decoded_data['Body']['stkCallback']['CheckoutRequestID'];

    if ($resultCode == 0) { // Payment was successful
        $amount = $decoded_data['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
        $mpesaReceiptNumber = $decoded_data['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
        $phoneNumber = $decoded_data['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'];

        // Save transaction details to the payments table
        $query = "INSERT INTO payments (user_id, phone_number, transaction_id, mpesa_receipt_number, amount, status) 
                  VALUES (1, '$phoneNumber', '$checkoutRequestID', '$mpesaReceiptNumber', '$amount', 'Completed')";
        mysqli_query($conn, $query);

        // Update landlord's premium status
        $updateQuery = "UPDATE landlords SET premium_status = 1 WHERE phone_number = '$phoneNumber'";
        mysqli_query($conn, $updateQuery);

        echo "Payment successful and landlord upgraded to premium!";
    } else {
        echo "Payment failed: $resultDesc";
    }
} else {
    echo "No data received from Safaricom.";
}
?>
