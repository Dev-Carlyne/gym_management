<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['id_number'])) {
    echo "Access denied. Please log in.";
    exit;
}

$id_number = $_SESSION['id_number'];

// Handle attendance marking
if (isset($_POST['mark_attended'])) {
    $booking_id = $_POST['booking_id'];

    // Insert or update attendance
    $check = mysqli_query($conn, "SELECT * FROM attendance WHERE booking_id='$booking_id'");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE attendance SET status='Attended', marked_at=NOW() WHERE booking_id='$booking_id'");
    } else {
        mysqli_query($conn, "INSERT INTO attendance (booking_id, status) VALUES ('$booking_id', 'Attended')");
    }
    echo "<p style='color:green;'>✅ Attendance marked successfully!</p>";
}

// Fetch booked classes for this client
$query = "
    SELECT b.booking_id, c.class_name, b.booking_date, a.status
    FROM bookings b
    JOIN classes c ON b.class_id = c.class_id
    LEFT JOIN attendance a ON b.booking_id = a.booking_id
    WHERE b.id_number = '$id_number'
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
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
    <title>My Attendance</title>
    <style> 
        body { margin: 0; font-family: Arial, sans-serif; background: url('images/1.jpeg') no-repeat center center fixed; background-size: cover; color: white; } 
        body::before { content: ""; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.6); z-index: -1; } .container { max-width: 600px; margin: 100px auto 80px; background: rgba(255, 255, 255, 0.1); padding: 30px; border-radius: 10px; text-align: center; } 
        h2 { margin-bottom: 20px; } p { font-size: 18px; margin-bottom: 30px; } 
        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: center;
            color: #fff;
        }

        th {
            background-color: rgba(0, 0, 0, 0.5);
        }
        footer { position: fixed; bottom: 0; background-color: rgba(0, 0, 0, 0.8); color: #ccc; text-align: center; width: 100%; padding: 15px; } 
    </style>
</head>
<body>
    <h2 style="text-align:center;">📅 My Attendance</h2>
    <table>
        <tr>
            <th>Class</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['class_name']; ?></td>
                <td><?= $row['booking_date']; ?></td>
                <td><?= $row['status'] ? $row['status'] : 'Not Marked'; ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="booking_id" value="<?= $row['booking_id']; ?>">
                        <button type="submit" name="mark_attended"
                            <?= ($row['status'] === 'Attended') ? 'disabled' : ''; ?>>
                            Mark Attended
                        </button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    <footer>
        <p>&copy; 2025 Fit Track. All rights reserved.</p>
    </footer>
</body>
</html>
