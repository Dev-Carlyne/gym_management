<?php
session_start();
include 'db_connect.php';
date_default_timezone_set('Africa/Nairobi');

// Safaricom sandbox credentials
$consumerKey = 'nKRO4CCJ7hRwAXcZGsrhdy6x5jyGhdVEaPwVeM1rcR5';
$consumerSecret = '9YPtGmtvc47qQyA0pGA5gwWprDHvdiwPJGtD5h6Ao7AJCRTtFKn3LXHvK3T0oJFC';
$shortCode = '174379';
$passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
$CallBackURL = "https://09f70606de1e.ngrok-free.app/gg_management/callback.php";

// Validate selected bookings
if (!isset($_POST['booking_ids']) || !is_array($_POST['booking_ids'])) {
    die("<div style='color:red;font-weight:bold;'>⚠ No classes selected for payment.</div>");
}

$booking_ids = $_POST['booking_ids'];
$phone_raw = mysqli_real_escape_string($conn, $_POST['phone']);

// Normalize phone number to 2547XXXXXXXX format
$phone = preg_replace('/\D/', '', $phone_raw);

if (preg_match('/^0\d{9}$/', $phone)) {
    // e.g., 07XXXXXXXX → 2547XXXXXXXX
    $phone = '254' . substr($phone, 1);
} elseif (preg_match('/^2547\d{8}$/', $phone)) {
    // already in correct format: do nothing
} else {
    die("<div style='color:red;font-weight:bold;'>⚠ Invalid phone number format. Please enter 07XXXXXXXX or 2547XXXXXXXX.</div>");
}

// Fetch booking prices
$ids_str = implode(",", array_map('intval', $booking_ids));
$query = "SELECT b.booking_id, c.price, c.class_name
          FROM bookings b
          JOIN classes c ON b.class_id = c.class_id
          WHERE b.booking_id IN ($ids_str)";
$result = mysqli_query($conn, $query);

$totalAmount = 0;
$bookings = [];
while ($row = mysqli_fetch_assoc($result)) {
    $totalAmount += $row['price'];
    $bookings[] = $row;
}

// Generate OAuth access token
$credentials = base64_encode($consumerKey . ':' . $consumerSecret);
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // optional in sandbox/testing
$response = curl_exec($curl);

if (!$response) {
    die("<div style='color:red;font-weight:bold;'>❌ Failed to connect to Safaricom: " . curl_error($curl) . "</div>");
}
$result = json_decode($response);
curl_close($curl);

$access_token = $result->access_token ?? null;
if (!$access_token) {
    die("<div style='color:red;font-weight:bold;'>❌ Could not get access token from Safaricom.</div>");
}

// Prepare STK Push: timestamp and password
$Timestamp = date('YmdHis');
$password = base64_encode($shortCode . $passkey . $Timestamp);

// Initiate STK Push request
$curl = curl_init();

// Temporary override (testing only) to bypass DNS resolution issues
curl_setopt($curl, CURLOPT_RESOLVE, ["sandbox.safaricom.co.ke:443:45.223.139.195"]);

curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization:Bearer ' . $access_token
]);

$curl_post_data = [
    'BusinessShortCode' => $shortCode,
    'Password' => $password,
    'Timestamp' => $Timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $totalAmount,
    'PartyA' => 254721234567,       // must be formatted 2547XXXXXXXX
    'PartyB' => $shortCode,
    'PhoneNumber' => 254721234567,  // should match PartyA
    'CallBackURL' => $CallBackURL,
    'AccountReference' => "GymBooking",
    'TransactionDesc' => "Payment for booked gym classes"
];

$data_string = json_encode($curl_post_data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

// Disable SSL verification temporarily (testing only)
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

$response = curl_exec($curl);

if (!$response) {
    die("<div style='color:red;font-weight:bold;'>❌ Curl error: " . curl_error($curl) . "</div>");
}

$response_data = json_decode($response, true);
curl_close($curl);

// Handle response: success vs error
if (isset($response_data['CheckoutRequestID'])) {
    $checkout_id = $response_data['CheckoutRequestID'];
    foreach ($bookings as $b) {
        $sql = "INSERT INTO payments (booking_id, amount, status, checkout_id, phone_number) 
                VALUES ('{$b['booking_id']}', '{$b['price']}', 'pending', '$checkout_id', '$phone')";
        mysqli_query($conn, $sql);
    }

    echo "<div style='padding:15px;background:#e0ffe0;border:1px solid #00a000;color:#006600;font-size:16px;border-radius:8px;'>
            ✅ Payment request sent successfully!<br>
            Please check your phone (<b>$phone_raw</b>) and enter your M‑PESA PIN to complete the payment of <b>KES $totalAmount</b>.
          </div>";
} else {
    $errorMsg = $response_data['errorMessage'] ?? 'Unknown error occurred.';
    echo "<div style='padding:15px;background:#ffe0e0;border:1px solid #a00000;color:#800000;font-size:16px;border-radius:8px;'>
            ❌ Payment initiation failed.<br>
            Reason: <b>$errorMsg</b>
          </div>";
    echo "<pre style='background:#f8f8f8;border:1px solid #ccc;padding:10px;margin-top:10px;'>" . print_r($response_data, true) . "</pre>";
}
?>
