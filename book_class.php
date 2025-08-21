<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_number = $_SESSION['id_number'] ?? null;
    $class_id = $_POST['class_id'] ?? null;

    if (!$id_number || !$class_id) {
        die("Missing id_number or class_id.");
    }

    // Check if user exists
    $check_user = mysqli_query($conn, "SELECT * FROM users WHERE id_number = '$id_number'");
    if (mysqli_num_rows($check_user) == 0) {
        die("User does not exist.");
    }

    // Check if class exists
    $checkClass = mysqli_query($conn, "SELECT * FROM classes WHERE class_id = '$class_id'");
    if (mysqli_num_rows($checkClass) == 0) {
        die("Error: Invalid class selected.");
    }

    // Check if class was booked in the last 14 days
    $check_query = "
        SELECT * FROM bookings 
        WHERE id_number = '$id_number' 
          AND class_id = '$class_id' 
          AND booking_date >= DATE_SUB(NOW(), INTERVAL 14 DAY)
    ";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo "You have already booked this class in the last 14 days.<br>";
        echo "<a href='client_dashboard.php'>Go to Dashboard</a>";
        exit;
    }

    // Insert booking
    $insert = "INSERT INTO bookings (id_number, class_id, booking_date) VALUES ('$id_number', '$class_id', NOW())";
    $insert_result = mysqli_query($conn, $insert);

    if ($insert_result) {
     $booking_id = mysqli_insert_id($conn);
     echo "<h2>Booking successful.</h2>";
     echo "<p><a href='payment.php?booking_id=$booking_id'>Proceed to Payment</a></p>";
     echo "<p><a href='client_dashboard.php'>Go to Dashboard</a></p>";
    } else {
     echo "Booking failed.";
    }
} else {
    die("Invalid request method.");
}
