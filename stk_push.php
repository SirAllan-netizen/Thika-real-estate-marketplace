<?php
include 'access_token.php'; // Make sure this file is in the same directory

function initiateStkPush($phoneNumber, $amount) {
    $shortCode = '174379'; // This is the Safaricom Lipa Na Mpesa Online Shortcode for testing
    $lipaNaMpesaOnlinePasskey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'; // Test Passkey from Safaricom
    $callbackUrl = 'https://yourdomain.com/Thikarealestate/callback.php'; // Update this to your actual URL when in production

    $timestamp = date('YmdHis');
    $password = base64_encode($shortCode . $lipaNaMpesaOnlinePasskey . $timestamp);

    // Generate access token from access_token.php
    $accessToken = generateMpesaAccessToken();

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $accessToken));

    $data = array(
        'BusinessShortCode' => $shortCode,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phoneNumber,
        'PartyB' => $shortCode,
        'PhoneNumber' => $phoneNumber,
        'CallBackURL' => $callbackUrl,
        'AccountReference' => 'PremiumUpgrade',
        'TransactionDesc' => 'Landlord Premium Upgrade Payment'
    );

    $dataString = json_encode($data);

    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $dataString);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response);
}

if (isset($_POST['phone_number'])) {
    $phoneNumber = $_POST['phone_number'];
    $amount = 1; // The amount to be charged for premium upgrade

    $response = initiateStkPush($phoneNumber, $amount);
    echo json_encode($response);
}
?>
