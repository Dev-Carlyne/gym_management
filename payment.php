<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'db_connect.php';

$booking_id = $_GET['booking_id'] ?? '';

if (!$booking_id) {
    echo "Missing booking ID.";
    exit;
}

// Get booking and class price
$query = "SELECT b.id_number, b.class_id, c.price 
          FROM bookings b 
          JOIN classes c ON b.class_id = c.class_id 
          WHERE b.booking_id = '$booking_id'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Booking not found.";
    exit;
}

$data = mysqli_fetch_assoc($result);
$amount = $data['price'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Payment</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('images/1.jpeg') no-repeat center center fixed;
            background-size: cover;
            color: white;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }

        .container {
            max-width: 600px;
            margin: 100px auto 80px;
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            margin-bottom: 30px;
        }

        button {
            padding: 12px 24px;
            font-size: 16px;
            background-color: #00b894;
            border: none;
            border-radius: 6px;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #019874;
        }

        footer {
            position: fixed;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.8);
            color: #ccc;
            text-align: center;
            width: 100%;
            padding: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Confirm Payment</h2>
    <p>Class Price: Ksh <?php echo $amount; ?></p>

    <form action="process_payment.php" method="POST">
        <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
        <input type="hidden" name="amount" value="<?php echo $amount; ?>">
        <button type="submit">Pay Now</button>
    </form>
</div>

<footer>
    <p>&copy; 2025 Fit Track. All rights reserved.</p>
</footer>

</body>
</html>
