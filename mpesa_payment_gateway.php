<?php
session_start();
require 'db_connect.php';

// Sandbox Credentials
$consumerKey = 'nPzPa2TqAWNLItxPtGFZAGnFu9vJfAbtC6RkCwXJtxmDFAjl';  // Your Consumer Key
$consumerSecret = 'T82fUaCXfXaYxNTdhopSEGAnRk9kD7l2cPACFUcjnU4hwqbFGZ91TprkOBmexZQn';  // Your Consumer Secret
$shortCode = '174379'; // Shortcode for testing in sandbox (ALWAYS use this for sandbox)
$passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2b5b1e93baf9b57e5b8b4f45f67b9651'; // Default Passkey for sandbox

// Use your generated Ngrok URL here (Replace this with your actual Ngrok URL)
$callbackUrl = $callbackUrl = 'https://1234abcd.ngrok.io/Thikarealestate/mpesa_callback.php';

// Get transaction details from URL
$transactionId = $_GET['transaction_id'];
$amount = $_GET['amount'];
$phone = $_GET['phone']; // Must be in the format 2547XXXXXXXX

// Generate Access Token
$authUrl = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$credentials = base64_encode($consumerKey . ':' . $consumerSecret);

$curl = curl_init($authUrl);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic $credentials"));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
$response = json_decode($response);

$accessToken = $response->access_token;

curl_close($curl);

if (!$accessToken) {
    die("Failed to generate access token. Please check your credentials.");
}

// Make the STK Push Request
$apiUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

$timestamp = date('YmdHis');
$password = base64_encode($shortCode . $passkey . $timestamp);

$data = array(
    'BusinessShortCode' => $shortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $amount,
    'PartyA' => $phone,
    'PartyB' => $shortCode,
    'PhoneNumber' => $phone,
    'CallBackURL' => $callbackUrl,
    'AccountReference' => $transactionId,
    'TransactionDesc' => 'Thika Real Estate Payment'
);

$curl = curl_init($apiUrl);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $accessToken));
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
curl_close($curl);

// Display response for debugging
echo $response;
