<?php
include 'db_connect.php';
session_start();

// ✅ EXPORT HANDLER — must be before any output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export'])) {

    $export_type = $_POST['export_type'] ?? '';
    $client_id   = trim($_POST['client_id'] ?? '');
    $client_id   = mysqli_real_escape_string($conn, $client_id);

    // Start CSV stream
    $filename = 'report_' . ($export_type ?: 'export') . '_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    $out = fopen('php://output', 'w');

    // Helper to run a query and dump rows
    $dump = function($headerCols, $sql) use ($conn, $out) {
        fputcsv($out, $headerCols);
        $res = mysqli_query($conn, $sql);
        if ($res && mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
                fputcsv($out, $row);
            }
        } else {
            fputcsv($out, ['No rows']);
        }
    };

    if ($export_type === 'all_bookings') {
        fputcsv($out, ['--- ALL BOOKINGS ---']);
        $sql = "
            SELECT 
                b.booking_id,
                u.name AS client_name,
                u.id_number,
                c.class_name,
                c.schedule,
                b.booking_date,
                b.booking_time,
                b.payment_status
            FROM bookings b
            JOIN users u  ON b.id_number = u.id_number
            JOIN classes c ON b.class_id = c.class_id
            ORDER BY b.booking_date DESC
        ";
        $dump(
            ['booking_id','client_name','id_number','class_name','schedule','booking_date','booking_time','payment_status'],
            $sql
        );

    } elseif ($export_type === 'all_payments') {
        fputcsv($out, ['--- ALL PAYMENTS ---']);
        $sql = "
            SELECT 
                p.payment_id,
                u.name AS client_name,
                u.id_number,
                c.class_name,
                p.amount,
                p.payment_method,
                p.payment_status,
                p.payment_date
            FROM payments p
            JOIN users u   ON p.id_number = u.id_number
            LEFT JOIN classes c ON p.class_id = c.class_id
            ORDER BY p.payment_date DESC
        ";
        $dump(
            ['payment_id','client_name','id_number','class_name','amount','payment_method','payment_status','payment_date'],
            $sql
        );

    } elseif ($export_type === 'attendance') {
        fputcsv($out, ['--- ATTENDANCE ---']);
        $sql = "
            SELECT 
                a.attendance_id,
                u.name AS client_name,
                u.id_number,
                c.class_name,
                a.booking_id,
                a.status,
                a.marked_at
            FROM attendance a
            JOIN bookings b ON a.booking_id = b.booking_id
            JOIN users u    ON b.id_number = u.id_number
            JOIN classes c  ON b.class_id = c.class_id
            ORDER BY a.marked_at DESC
        ";
        $dump(
            ['attendance_id','client_name','id_number','class_name','booking_id','status','marked_at'],
            $sql
        );

    } elseif ($export_type === 'by_client' && $client_id !== '') {
        // One file with three sections for a single client
        // --- BOOKINGS ---
        fputcsv($out, ['--- BOOKINGS (Client '.$client_id.') ---']);
        $sqlB = "
            SELECT 
                b.booking_id,
                u.name AS client_name,
                u.id_number,
                c.class_name,
                c.schedule,
                b.booking_date,
                b.booking_time,
                b.payment_status
            FROM bookings b
            JOIN users u  ON b.id_number = u.id_number
            JOIN classes c ON b.class_id = c.class_id
            WHERE b.id_number = '$client_id'
            ORDER BY b.booking_date DESC
        ";
        $dump(
            ['booking_id','client_name','id_number','class_name','schedule','booking_date','booking_time','payment_status'],
            $sqlB
        );

        // --- PAYMENTS ---
        fputcsv($out, []); fputcsv($out, ['--- PAYMENTS (Client '.$client_id.') ---']);
        $sqlP = "
            SELECT 
                p.payment_id,
                u.name AS client_name,
                u.id_number,
                c.class_name,
                p.amount,
                p.payment_method,
                p.payment_status,
                p.payment_date
            FROM payments p
            JOIN users u   ON p.id_number = u.id_number
            LEFT JOIN classes c ON p.class_id = c.class_id
            WHERE p.id_number = '$client_id'
            ORDER BY p.payment_date DESC
        ";
        $dump(
            ['payment_id','client_name','id_number','class_name','amount','payment_method','payment_status','payment_date'],
            $sqlP
        );

        // --- ATTENDANCE ---
        fputcsv($out, []); fputcsv($out, ['--- ATTENDANCE (Client '.$client_id.') ---']);
        $sqlA = "
            SELECT 
                a.attendance_id,
                u.name AS client_name,
                u.id_number,
                c.class_name,
                a.booking_id,
                a.status,
                a.marked_at
            FROM attendance a
            JOIN bookings b ON a.booking_id = b.booking_id
            JOIN users u    ON b.id_number = u.id_number
            JOIN classes c  ON b.class_id = c.class_id
            WHERE b.id_number = '$client_id'
            ORDER BY a.marked_at DESC
        ";
        $dump(
            ['attendance_id','client_name','id_number','class_name','booking_id','status','marked_at'],
            $sqlA
        );

    } else {
        fputcsv($out, ['Invalid export selection or missing client ID.']);
    }

    fclose($out);
    exit;
}
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
            backdrop-filter: blur(10px);
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
    <a href="admin_dashboard.php" 
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
<div class="overlay">
    <h1>All Client Activity Reports</h1>

    <!-- Export Form -->
     <div class="export-form">
        <form method="POST">
            <label for="export_type">Export Report:</label>
            <select name="export_type" required>
                <option value="all_bookings">All Bookings</option>
                <option value="all_payments">All Payments</option>
                <option value="attendance">Attendance</option>
                <option value="by_client">By Client</option>
            </select>
            <input type="text" name="id_number" placeholder="Enter Client ID (for specific client)">
            <button type="submit" name="export">Export</button>
        </form>
    </div>

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

    <!-- All Attendance -->
    <h2>✔ Attendance</h2>
    <?php
    $attendance_query = "
        SELECT a.attendance_id, a.status, a.marked_at, 
            u.name, u.id_number, 
            c.class_name, c.schedule
        FROM attendance a
        JOIN bookings b ON a.booking_id = b.booking_id
        JOIN classes c ON b.class_id = c.class_id
        JOIN users u ON b.id_number = u.id_number
        ORDER BY a.marked_at DESC
    ";

    $result = mysqli_query($conn, $attendance_query);

    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table>
                <tr>
                    <th>Client ID</th>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Schedule</th>
                    <th>Status</th>
                    <th>Marked At</th>
                </tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['id_number']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['class_name']}</td>
                    <td>{$row['schedule']}</td>
                    <td>{$row['status']}</td>
                    <td>{$row['marked_at']}</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No attendance records found.</p>";
    }
    ?>
    
    <!-- All Feedback -->
       <h2>📝 Feedback</h2>
    <?php
    $feedback_query = "
        SELECT f.message, f.submitted_at, f.source, u.name, u.id_number
        FROM feedback f
        JOIN users u ON f.id_number = u.id_number
        ORDER BY f.submitted_at DESC
    ";

    $result = mysqli_query($conn, $feedback_query);

    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table border='1' cellpadding='8' cellspacing='0'>
                <tr style='background-color:#f2f2f2;'>
                    <th>Client ID</th>
                    <th>Name</th>
                    <th>Message</th>
                    <th>Source</th>
                    <th>Submitted At</th>
                </tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['id_number'] ?? '-';
            $name = $row['source'] === 'public' ? 'Public User' : ($row['name'] ?? 'Unknown');
            $message = $row['message'];
            $date = $row['submitted_at'];

            echo "<tr>
                    <td>{$row['id_number']}</td>
                    <td>{$name}</td>
                    <td>{$message}</td>
                    <td>{$row['source']}</td>
                    <td>{$date}</td>
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