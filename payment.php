<?php
session_start();
include 'db_connect.php';

$id_number = $_SESSION['id_number']; // client logged in

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);

    // Get booking + class details
    $stmt = $conn->prepare("SELECT b.booking_id, b.class_id, c.class_name, c.price 
                            FROM bookings b 
                            JOIN classes c ON b.class_id = c.class_id 
                            WHERE b.booking_id = ? AND b.id_number = ?");
    $stmt->bind_param("is", $booking_id, $id_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();

    if ($booking) {
        // Call stk_push.php with booking info
        $amount = $booking['price'];
        $class_id = $booking['class_id'];

        // ---- STK Push ----
        // Replace with real Daraja credentials from https://developer.safaricom.co.ke
        $consumerKey = 'nKRO4CCJ7hRwAXcZGsrhdy6x5jyGhdVEaPwVeMZwWeM1rcR5';
        $consumerSecret = '9YPtGmtvc47qQyA0pGA5gwWprDHvdiwPJGtD5h6Ao7AJCRTtFKn3LXHvK3T0oJFC';
        $shortCode = '174379'; // Till/Paybill number
        $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
        $phone = '2547XXXXXXXX'; // Client phone (from DB/session)
        $callbackURL = "https://YOUR_DOMAIN_OR_NGROK/gg_management/callback.php";

        // Get access token
        $headers = ['Content-Type:application/json; charset=utf8'];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
        $result = curl_exec($curl);

        if (!$result) {
            die("cURL Error: " . curl_error($curl));
        }
        $response = json_decode($result);
        if (!isset($response->access_token)) {
            die("Failed to get access token. Response: " . $result);
        }
        $access_token = $response->access_token;
        curl_close($curl);

        $Timestamp = date('YmdHis');
        $Password = base64_encode($shortCode . $passkey . $Timestamp);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization:Bearer '.$access_token]);

        $curl_post_data = [
            'BusinessShortCode' => $shortCode,
            'Password' => $Password,
            'Timestamp' => $Timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => $shortCode,
            'PhoneNumber' => $phone,
            'CallBackURL' => $callbackURL,
            'AccountReference' => "Booking".$booking_id,
            'TransactionDesc' => "Payment for ".$booking['class_name']
        ];

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
        $response = curl_exec($curl);
        curl_close($curl);

        $responseData = json_decode($response, true);

        if (isset($responseData['CheckoutRequestID'])) {
            $checkoutRequestID = $responseData['CheckoutRequestID'];

            // Save checkout_id in bookings
            $stmt = $conn->prepare("UPDATE bookings SET checkout_id = ? WHERE booking_id = ?");
            $stmt->bind_param("si", $checkoutRequestID, $booking_id);
            $stmt->execute();
            $stmt->close();

            // Create pending payment entry
            $stmt2 = $conn->prepare("INSERT INTO payments (id_number, class_id, booking_id, payment_method, payment_status, payment_date) 
                                     VALUES (?, ?, ?, 'M-PESA', 'pending', NOW())");
            $stmt2->bind_param("sii", $id_number, $class_id, $booking_id);
            $stmt2->execute();
            $stmt2->close();

            echo "<p>Payment request sent to your phone. Please complete the transaction.</p>";
        } else {
            echo "<p>Error initiating STK push: " . $response . "</p>";
        }
    } else {
        echo "<p>Invalid booking.</p>";
    }
    exit;
}

    // Display available bookings to pay
    $query = "SELECT b.booking_id, c.class_name, c.price 
            FROM bookings b 
            JOIN classes c ON b.class_id = c.class_id 
            WHERE b.id_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id_number);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>
<!DOCTYPE html> 
<html lang="en">  
<head>
<meta charset="UTF-8">
<title>Checkout - Select Booking to Pay</title> 
    <style> 
        body { margin: 0; font-family: Arial, sans-serif; background: url('images/1.jpeg') no-repeat center center fixed; background-size: cover; color: white; } body::before { content: ""; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.6); z-index: -1; } .container { max-width: 600px; margin: 100px auto 80px; background: rgba(255, 255, 255, 0.1); padding: 30px; border-radius: 10px; text-align: center; } h2 { margin-bottom: 20px; } p { font-size: 18px; margin-bottom: 30px; } button { padding: 12px 24px; font-size: 16px; background-color: #00b894; border: none; border-radius: 6px; color: white; cursor: pointer; } button:hover { background-color: #019874; } footer { position: fixed; bottom: 0; background-color: rgba(0, 0, 0, 0.8); color: #ccc; text-align: center; width: 100%; padding: 15px; } 
    </style> 
</head> 
<body> 
<div style="text-align: center;">
        <a href="client_dashboard.php" 
        style="
        display: inline-block;
        background-color: #2563eb;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        margin: 20px 0;
     ">← Back to Dashboard</a>
    </div>
<div class="container">

<h2>Checkout - Select Booking to Pay</h2>
<form method="POST">
    <table border="1" cellpadding="10">
        <tr>
            <th>Class</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                <td>KES <?php echo htmlspecialchars($row['price']); ?></td>
                <td>
                    <button type="submit" name="booking_id" value="<?php echo $row['booking_id']; ?>">Pay</button>
                </td>
            </tr>
        <?php } ?>
    </table>
</form>
</div> 

<footer> 
    <p>&copy; 2025 Fit Track. All rights reserved.</p> 
</footer> 
</body> 
</html>