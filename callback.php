<?php
// callback.php
header("Content-Type: application/json");

// 1. Read raw Safaricom JSON callback
$callbackJSON = file_get_contents('php://input');
$callbackData = json_decode($callbackJSON, true);

// 2. Log everything for debugging
file_put_contents('callback_log.txt', date("Y-m-d H:i:s") . " - CALLBACK RECEIVED\n", FILE_APPEND);
file_put_contents('callback_log.txt', print_r($callbackData, true) . "\n\n", FILE_APPEND);

// 3. Always acknowledge Safaricom to avoid retry spam
echo json_encode(["ResultCode" => 0, "ResultDesc" => "Accepted"]);

// 4. Process payment if data exists
if (isset($callbackData['Body']['stkCallback'])) {
    $stkCallback = $callbackData['Body']['stkCallback'];
    $resultCode = $stkCallback['ResultCode'];
    $checkoutRequestID = $stkCallback['CheckoutRequestID'];

    include 'db_connect.php'; // Make sure this connects to your DB

    if ($resultCode == 0 && isset($stkCallback['CallbackMetadata']['Item'])) {
        $metadata = $stkCallback['CallbackMetadata']['Item'];

        // Extract values safely
        $amount = null;
        $mpesaReceipt = null;
        $phone = null;

        foreach ($metadata as $item) {
            if ($item['Name'] === 'Amount') {
                $amount = $item['Value'];
            }
            if ($item['Name'] === 'MpesaReceiptNumber') {
                $mpesaReceipt = $item['Value'];
            }
            if ($item['Name'] === 'PhoneNumber') {
                $phone = $item['Value'];
            }
        }

        // Save to DB
        if ($amount && $mpesaReceipt && $phone) {
            $stmt = $conn->prepare("INSERT INTO payments (checkout_id, amount, mpesa_receipt, phone, status) VALUES (?, ?, ?, ?, 'Success')");
            $stmt->bind_param("sdss", $checkoutRequestID, $amount, $mpesaReceipt, $phone);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        // Save failed transaction
        $stmt = $conn->prepare("INSERT INTO payments (checkout_id, status) VALUES (?, 'Failed')");
        $stmt->bind_param("s", $checkoutRequestID);
        $stmt->execute();
        $stmt->close();
    }
}
