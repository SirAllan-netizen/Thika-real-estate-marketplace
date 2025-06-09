<?php
include 'mpesa_config.php';

function generateAccessToken() {
    global $consumerKey, $consumerSecret;

    $credentials = base64_encode($consumerKey . ':' . $consumerSecret);
    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);
    $result = json_decode($response);

    if (isset($result->access_token)) {
        return $result->access_token;
    } else {
        die('Failed to generate access token.');
    }
}
