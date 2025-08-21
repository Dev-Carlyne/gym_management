<?php
date_default_timezone_set('Africa/Nairobi');

# 1. Your sandbox credentials
$consumerKey = 'nKRO4CCJ7hRwAXcZGsrhdy6x5jyGhdVEaPwVeMZwWeM1rcR5';
$consumerSecret = '9YPtGmtvc47qQyA0pGA5gwWprDHvdiwPJGtD5h6Ao7AJCRTtFKn3LXHvK3T0oJFC';
$shortCode = '174379'; // Sandbox test PayBill
$passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'; // Get from developer portal

# 2. Generate access token
$headers = ['Content-Type:application/json; charset=utf8'];
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
$result = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$result = json_decode($result);
$access_token = $result->access_token;
curl_close($curl);

# 3. Prepare STK push payload
$Timestamp = date('YmdHis');
$password = base64_encode($shortCode . $passkey . $Timestamp);
$CallBackURL = "https://09f70606de1e.ngrok-free.app/gg_management/callback.php"; // Replace with ngrok link

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $access_token));

$curl_post_data = [
    'BusinessShortCode' => $shortCode,
    'Password' => $password,
    'Timestamp' => $Timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => 1, // test amount
    'PartyA' => '254708374149', // sandbox test phone
    'PartyB' => $shortCode,
    'PhoneNumber' => '254708374149',
    'CallBackURL' => $CallBackURL,
    'AccountReference' => 'Test123',
    'TransactionDesc' => 'Sandbox payment'
];

$data_string = json_encode($curl_post_data);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
$response = curl_exec($curl);
echo $response;
