<?php
// Define Consumer Key and Consumer Secret as constants
define('MPESA_CONSUMER_KEY', 'nPzPa2TqAWNLItxPtGFZAGnFu9vJfAbtC6RkCwXJtxmDFAjl');  // Replace with your actual Consumer Key
define('MPESA_CONSUMER_SECRET', 'T82fUaCXfXaYxNTdhopSEGAnRk9kD7l2cPACFUcjnU4hwqbFGZ91TprkOBmexZQn');  // Replace with your actual Consumer Secret

function generateMpesaAccessToken() {
    // Safaricom OAuth2 token URL
    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    // Combine Consumer Key and Consumer Secret to generate the Authorization header
    $credentials = base64_encode(MPESA_CONSUMER_KEY . ':' . MPESA_CONSUMER_SECRET);

    // Prepare headers
    $headers = [
        "Authorization: Basic $credentials"
    ];

    // Initialize cURL to send the request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HEADER, false);

    // Execute cURL request and get the response
    $response = curl_exec($ch);

    // Check if the request was successful
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        return false;  // Return false in case of an error
    }

    curl_close($ch);

    // Decode the response
    $responseObj = json_decode($response);

    // Check if we got a valid access token in the response
    if (isset($responseObj->access_token)) {
        // Return access token if it exists
        return $responseObj->access_token;
    } else {
        // If there's no access token, handle the error (e.g., invalid credentials, API limits, etc.)
        echo 'Error: Could not retrieve access token. Response: ' . json_encode($responseObj);
        return false;  // Return false if access token is not available
    }
}
?>
