<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['id_number'])) {
    echo "Access denied. Please log in.";
    exit;
}

$id_number = $_SESSION['id_number'];

// Handle filters
$where = "WHERE b.id_number = '$id_number'";
if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    $from = $_GET['from_date'];
    $to = $_GET['to_date'];
    $where .= " AND p.payment_date BETWEEN '$from' AND '$to'";
}
if (!empty($_GET['status'])) {
    $status = $_GET['status'];
    $where .= " AND p.payment_status = '$status'";
}
if (!empty($_GET['class_name'])) {
    $class = $_GET['class_name'];
    $where .= " AND c.class_name LIKE '%$class%'";
}

$query = "
    SELECT p.payment_id, p.booking_id, p.amount, p.payment_status, p.payment_date, c.class_name
    FROM payments p
    JOIN bookings b ON p.booking_id = b.booking_id
    JOIN classes c ON b.class_id = c.class_id
    $where
    ORDER BY p.payment_date DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment History</title>
    <style> body { font-family: Arial, sans-serif; padding: 20px; } table { border-collapse: collapse; width: 100%; margin-top: 20px; } th, td { border: 1px solid #ccc; padding: 10px; text-align: left; } th { background-color: #f2f2f2; } input, select, button { margin-right: 10px; padding: 5px; } .filter-form { margin-bottom: 20px; } a { text-decoration: none; color: #007bff; } body { font-family: Arial, sans-serif; margin: 0; background: url('images/1.jpeg') no-repeat center center fixed; background-size: cover; color: #f0f0f0; position: relative; min-height: 100vh; } body::before { content: ""; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); z-index: -1; } .container { padding: 30px 20px 100px; max-width: 1000px; margin: auto; } h2 { color: #ffffff; text-align: center; } .filter-form { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-bottom: 20px; } .filter-form label { color: #f0f0f0; } input, select, button { padding: 7px; border: none; border-radius: 4px; } button { background-color: #007bff; color: white; cursor: pointer; } a { color: #00ccff; text-decoration: none; } table { width: 100%; border-collapse: collapse; background-color: rgba(255, 255, 255, 0.1); } th, td { border: 1px solid #ddd; padding: 10px; text-align: left; color: #fff; } th { background-color: rgba(255, 255, 255, 0.2); } footer { bottom: 0; width: 100%; text-align: center; background: rgba(0, 0, 0, 0.8); padding: 10px 0; position: fixed; color: #bbb; } </style>
</head>
<body>
    <h2>My Payment History</h2>

    <form method="GET" class="filter-form">
        <label>From: <input type="date" name="from_date" value="<?= $_GET['from_date'] ?? '' ?>"></label>
        <label>To: <input type="date" name="to_date" value="<?= $_GET['to_date'] ?? '' ?>"></label>
        <label>Status: 
            <select name="status">
                <option value="">All</option>
                <option value="Paid" <?= (($_GET['status'] ?? '') == 'Paid') ? 'selected' : '' ?>>Paid</option>
                <option value="Pending" <?= (($_GET['status'] ?? '') == 'Pending') ? 'selected' : '' ?>>Pending</option>
                <option value="Failed" <?= (($_GET['status'] ?? '') == 'Failed') ? 'selected' : '' ?>>Failed</option>
            </select>
        </label>
        <label>Class: <input type="text" name="class_name" placeholder="Class name..." value="<?= $_GET['class_name'] ?? '' ?>"></label>
        <button type="submit">Filter</button>
        <a href="view_payment.php">Reset</a>
    </form>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <tr>
                <th>Payment ID</th>
                <th>Class Name</th>
                <th>Amount (Ksh)</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['payment_id']; ?></td>
                    <td><?= $row['class_name']; ?></td>
                    <td><?= $row['amount']; ?></td>
                    <td class="<?= strtolower($row['payment_status']); ?>">
                        <?= $row['payment_status']; ?>
                    </td>
                    <td><?= $row['payment_date']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No payment records match your filters.</p>
    <?php endif; ?>

    <br>
    <a href="client_dashboard.php">← Back to Dashboard</a>
</body>
</html>