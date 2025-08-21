<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['id_number'])) {
    echo "Access denied. Please log in.";
    exit;
}

$id_number = $_SESSION['id_number'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Client Activity Report</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('images/7.jpeg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            color: #fff;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.7);
            min-height: 100vh;
            padding: 30px;
            padding-bottom: 100px;
        }

        h1, h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 30px;
        }

        table {
            border-collapse: collapse;
            width: 95%;
            margin: 30px auto;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
            color: #fff;
        }

        th {
            background-color: rgba(0, 0, 0, 0.6);
        }

        p {
            text-align: center;
            color: #ddd;
        }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.8);
            text-align: center;
            padding: 15px;
            color: #fff;
            font-size: 14px;
        }
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
    
    <h1>Your Activity History</h1>

    <!-- Booked Classes -->
    <h2>📅 Classes Booked</h2>
    <?php
    $bookings_query = "
        SELECT b.booking_date, c.class_name, c.schedule 
        FROM bookings b
        JOIN classes c ON b.class_id = c.class_id
        WHERE b.id_number = '$id_number'
        ORDER BY b.booking_date DESC
    ";
    $bookings_result = mysqli_query($conn, $bookings_query);

    if (mysqli_num_rows($bookings_result) > 0) {
        echo "<table><tr><th>Class Name</th><th>Schedule</th><th>Booking Date</th></tr>";
        while ($row = mysqli_fetch_assoc($bookings_result)) {
            echo "<tr>
                    <td>{$row['class_name']}</td>
                    <td>{$row['schedule']}</td>
                    <td>{$row['booking_date']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No class bookings found.</p>";
    }
    ?>

    <!-- Payments -->
    <h2>💳 Payments Made</h2>
    <?php
    $payments_query = "
        SELECT p.amount, p.payment_status, p.payment_date, c.class_name
        FROM payments p
        JOIN bookings b ON p.booking_id = b.booking_id
        JOIN classes c ON b.class_id = c.class_id
        WHERE b.id_number = '$id_number'
        ORDER BY p.payment_date DESC
    ";
    $payments_result = mysqli_query($conn, $payments_query);

    if (mysqli_num_rows($payments_result) > 0) {
        echo "<table><tr><th>Class</th><th>Amount</th><th>Status</th><th>Date</th></tr>";
        while ($row = mysqli_fetch_assoc($payments_result)) {
            echo "<tr>
                    <td>{$row['class_name']}</td>
                    <td>Ksh {$row['amount']}</td>
                    <td>{$row['payment_status']}</td>
                    <td>{$row['payment_date']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No payment records found.</p>";
    }
    ?>

    <!-- Feedback -->
    <h2>📝 Feedback Given</h2>
    <?php
    $feedback_query = "
        SELECT message, submitted_at 
        FROM feedback 
        WHERE id_number = '$id_number'
        ORDER BY submitted_at DESC
    ";
    $feedback_result = mysqli_query($conn, $feedback_query);

    if (mysqli_num_rows($feedback_result) > 0) {
        echo "<table><tr><th>Message</th><th>Date</th></tr>";
        while ($row = mysqli_fetch_assoc($feedback_result)) {
            echo "<tr>
                    <td>{$row['message']}</td>
                    <td>{$row['submitted_at']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No feedback found.</p>";
    }
    ?>
</body>
</html>
