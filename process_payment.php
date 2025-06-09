<?php
session_start();
include 'mpesa_config.php';
include 'access_token.php';
include 'db_connect.php';

$listing_id = $_GET['listing_id'];
$phone = "2547XXXXXXXX"; // Change this to your Mpesa phone number for testing

$amount = 500; // Cost of a premium listing
$accessToken = generateAccessToken();
$timestamp = date('YmdHis');
$password = base64_encode($shortcode . $passkey . $timestamp);

$url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

$curl_post_data = [
    'BusinessShortCode' => $shortcode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $amount,
    'PartyA' => $phone,
    'PartyB' => $shortcode,
    'PhoneNumber' => $phone,
    'CallBackURL' => $callbackUrl,
    'AccountReference' => "ThikaRealEstate",
    'TransactionDesc' => "Premium Listing Payment"
];

$data_string = json_encode($curl_post_data);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $accessToken
));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

$response = curl_exec($curl);
curl_close($curl);

echo $response;
