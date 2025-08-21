<?php
$consumerKey = 'YOUR_CONSUMER_KEY';
$consumerSecret = 'YOUR_CONSUMER_SECRET';
$businessShortCode = '174379'; // Sandbox
$passkey = 'YOUR_PASSKEY';
$checkoutRequestID = $_GET['checkout_id']; // get from URL

// Generate Access Token
$credentials = base64_encode($consumerKey . ':' . $consumerSecret);
$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);

$access_token = json_decode($response)->access_token;

// Prepare Query
$timestamp = date('YmdHis');
$password = base64_encode($businessShortCode . $passkey . $timestamp);

$curl_post_data = [
    'BusinessShortCode' => $businessShortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'CheckoutRequestID' => $checkoutRequestID,
];

$url = 'https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query';
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access_token
]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
$response = curl_exec($curl);
curl_close($curl);

echo $response;
