<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['booking_id']) && !empty($_POST['booking_id'])) {
        $booking_id = $_POST['booking_id'];

        // Check if the booking exists
        $booking_query = "SELECT b.*, c.price FROM bookings b 
                          JOIN classes c ON b.class_id = c.class_id 
                          WHERE b.booking_id = '$booking_id'";
        $booking_result = mysqli_query($conn, $booking_query);

        if (mysqli_num_rows($booking_result) == 1) {
            $booking = mysqli_fetch_assoc($booking_result);
            $amount = $booking['price'];

            // Check if payment has already been made
            $check_payment = mysqli_query($conn, "SELECT * FROM payments WHERE booking_id = '$booking_id'");
            if (mysqli_num_rows($check_payment) > 0) {
                echo "Payment already completed for this booking.<br><a href='client_dashboard.html'>Go to Dashboard</a>";
                exit;
            }

            // Simulate MPESA payment
            $payment_status = "Paid";
            $payment_date = date('Y-m-d H:i:s');

            $class_id = $booking['class_id'];
            $id_number = $booking['id_number']; // Also required
            
            $insert_query = "INSERT INTO payments (booking_id, id_number, class_id, amount, payment_status, payment_date)
                             VALUES ('$booking_id', '$id_number', '$class_id', '$amount', '$payment_status', '$payment_date')";
            
            

            if (mysqli_query($conn, $insert_query)) {
                echo "Payment successful for Booking ID: $booking_id (Amount: Ksh $amount)<br>";
                echo "<a href='client_dashboard.php'>Go to Dashboard</a>";
            } else {
                echo "Error recording payment: " . mysqli_error($conn);
            }
        } else {
            echo "Booking not found.<br><a href='view_classes.php'>Try Again</a>";
        }
    } else {
        echo "Missing booking ID.<br><a href='view_classes.php'>Go Back</a>";
    }
} else {
    echo "Invalid request method.";
}
?>
