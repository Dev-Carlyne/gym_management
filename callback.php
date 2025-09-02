<?php
// callback.php
header("Content-Type: application/json");

$callbackJSON = file_get_contents('php://input');
$callbackData = json_decode($callbackJSON, true);

// Log callback
file_put_contents('callback_log.txt', date("Y-m-d H:i:s")." - CALLBACK RECEIVED\n", FILE_APPEND);
file_put_contents('callback_log.txt', print_r($callbackData, true)."\n\n", FILE_APPEND);

echo json_encode(["ResultCode" => 0, "ResultDesc" => "Accepted"]);

if (isset($callbackData['Body']['stkCallback'])) {
    $stkCallback = $callbackData['Body']['stkCallback'];
    $resultCode = $stkCallback['ResultCode'];
    $checkoutRequestID = $stkCallback['CheckoutRequestID'];

    include 'db_connect.php';

    if ($resultCode == 0 && isset($stkCallback['CallbackMetadata']['Item'])) {
        $metadata = $stkCallback['CallbackMetadata']['Item'];

        $amount = null;
        $mpesaReceipt = null;
        $phone = null;

        foreach ($metadata as $item) {
            if ($item['Name'] === 'Amount') $amount = $item['Value'];
            if ($item['Name'] === 'MpesaReceiptNumber') $mpesaReceipt = $item['Value'];
            if ($item['Name'] === 'PhoneNumber') $phone = $item['Value'];
        }

        if ($amount && $mpesaReceipt && $phone) {
            // Get booking by checkout_id
            $stmt = $conn->prepare("SELECT booking_id FROM bookings WHERE checkout_id = ?");
            $stmt->bind_param("s", $checkoutRequestID);
            $stmt->execute();
            $stmt->bind_result($booking_id);
            $stmt->fetch();
            $stmt->close();

            if ($booking_id) {
                // Update payment
                $stmt2 = $conn->prepare("UPDATE payments 
                                         SET payment_status = 'Success', payment_date = NOW() 
                                         WHERE booking_id = ?");
                $stmt2->bind_param("i", $booking_id);
                $stmt2->execute();
                $stmt2->close();
            }
        }
    } else {
        // If failed, mark payment as failed
        $stmt = $conn->prepare("UPDATE payments SET payment_status = 'Failed' 
                                WHERE booking_id = (SELECT booking_id FROM bookings WHERE checkout_id = ? LIMIT 1)");
        $stmt->bind_param("s", $checkoutRequestID);
        $stmt->execute();
        $stmt->close();
    }
}
