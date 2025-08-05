<?php
include 'db_connect.php';
session_start();

// Optional: check if admin is logged in
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Client Activity Report</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('images/1.jpeg') no-repeat center center fixed;
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
<div class="overlay">
    <h1>All Client Activity Reports</h1>

    <!-- All Bookings -->
    <h2>📅 Classes Booked</h2>
    <?php
    $bookings_query = "
        SELECT b.booking_date, c.class_name, c.schedule, u.name, u.id_number
        FROM bookings b
        JOIN classes c ON b.class_id = c.class_id
        JOIN users u ON b.id_number = u.id_number
        ORDER BY b.booking_date DESC
    ";
    $result = mysqli_query($conn, $bookings_query);
    if (mysqli_num_rows($result) > 0) {
        echo "<table><tr><th>Client ID</th><th>Name</th><th>Class</th><th>Schedule</th><th>Booking Date</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['id_number']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['class_name']}</td>
                    <td>{$row['schedule']}</td>
                    <td>{$row['booking_date']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No bookings found.</p>";
    }
    ?>

    <!-- All Payments -->
    <h2>💳 Payments</h2>
    <?php
    $payments_query = "
        SELECT p.amount, p.payment_status, p.payment_date, c.class_name, u.name, u.id_number
        FROM payments p
        JOIN bookings b ON p.booking_id = b.booking_id
        JOIN classes c ON b.class_id = c.class_id
        JOIN users u ON b.id_number = u.id_number
        ORDER BY p.payment_date DESC
    ";
    $result = mysqli_query($conn, $payments_query);
    if (mysqli_num_rows($result) > 0) {
        echo "<table><tr><th>Client ID</th><th>Name</th><th>Class</th><th>Amount</th><th>Status</th><th>Payment Date</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['id_number']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['class_name']}</td>
                    <td>Ksh {$row['amount']}</td>
                    <td>{$row['payment_status']}</td>
                    <td>{$row['payment_date']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No payments found.</p>";
    }
    ?>

    <!-- All Feedback -->
    <h2>📝 Feedback</h2>
    <?php
    $feedback_query = "
        SELECT f.message, f.submitted_at, u.name, u.id_number
        FROM feedback f
        JOIN users u ON f.id_number = u.id_number
        ORDER BY f.submitted_at DESC
    ";
    $result = mysqli_query($conn, $feedback_query);
    if (mysqli_num_rows($result) > 0) {
        echo "<table><tr><th>Client ID</th><th>Name</th><th>Message</th><th>Date</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['id_number']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['message']}</td>
                    <td>{$row['submitted_at']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No feedback found.</p>";
    }
    ?>
</div>
<footer>
    <p>&copy; 2025 Fit Track. All rights reserved.</p>
</footer>
</body>
</html>
