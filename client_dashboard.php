<?php
include 'db_connect.php';
session_start();

// Check if logged in as client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit();
}

$client_id = $_SESSION['user_id'];

// Count unread messages
$msg_sql = "SELECT COUNT(*) AS unread_count FROM messages WHERE receiver_id = '$client_id' AND is_read = 0";
$msg_result = mysqli_query($conn, $msg_sql);
$msg_row = mysqli_fetch_assoc($msg_result);
$unread_messages = $msg_row['unread_count'];

// Count new notifications
$notif_sql = "SELECT COUNT(*) AS new_notifs FROM notifications WHERE is_active = 1";
$notif_result = mysqli_query($conn, $notif_sql);
$notif_row = mysqli_fetch_assoc($notif_result);
$new_notifs = $notif_row['new_notifs'];

$sql = "SELECT * FROM inventory WHERE quantity > 0";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: url('images/1.jpeg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* HEADER */
        .dashboard {
            background-color: rgba(31, 41, 55, 0.95);
            color: #fff;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 10;
        }
        .dashboard h1 {
            margin: 0;
            font-size: 22px;
        }
        .dashboard p {
            margin: 0;
            font-size: 12px;
        }

        /* SIDEBAR */
        .sidebar {
            height: 100%;
            width: 0;
            position: fixed;
            top: 0;
            left: 0;
            background: #111827;
            overflow-x: hidden;
            transition: 0.3s;
            padding-top: 60px;
            z-index: 20;
        }
        .sidebar a {
            padding: 12px 24px;
            text-decoration: none;
            font-size: 16px;
            color: #f3f4f6;
            display: block;
            transition: 0.2s;
        }
        .sidebar a:hover {
            background: #374151;
        }
        .close-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            color: #f3f4f6;
            cursor: pointer;
        }

        .menu-btn {
            font-size: 24px;
            cursor: pointer;
            background: none;
            border: none;
            color: #fff;
        }

        /* ICONS TOP-RIGHT */
        .top-icons {
            display: flex;
            gap: 36px;
            align-items: center;
        }
        .icon-box {
            position: relative;
            text-align: center;
            color: #fff;
        }
        .icon-box a {
            font-size: 24px;
            text-decoration: none;
            color: #fff;
        }
        .icon-label {
            font-size: 14px;
            margin-top: 4px;
        }
        .badge {
            position: absolute;
            top: -6px;
            right: 12px;
            background: red;
            color: white;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 50%;
        }
        /* Dropdown styles */
        .dropdown {
            position: relative;
            display: inline-block;
            text-align: center;
        }

        .dropbtn {
            background: none;
            border: none;
            cursor: pointer;
            color: white;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: 40px;
            right: 0;
            background-color: #1f2937;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            border-radius: 6px;
            z-index: 10;
        }

        .dropdown-content a {
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        .dropdown-content a:hover {
            background-color: #2563eb;
        }

        /* Show dropdown on hover */
        .dropdown:hover .dropdown-content {
            display: block;
        }

        /* INVENTORY */
        .section-header {
            text-align: center;
            background: #fff;
            padding: 20px;
            margin-top: 20px;
        }
        .inventory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            padding: 30px;
        }
        .item-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .item-card:hover {
            transform: translateY(-6px);
        }
        .item-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .item-content {
            padding: 16px;
        }
        .item-content h3 {
            margin: 0 0 8px;
            font-size: 18px;
        }
        .item-content p {
            margin: 4px 0;
            font-size: 14px;
            color: #555;
        }
        .item-content strong {
            color: #111827;
        }
        .btn {
            display: inline-block;
            margin-top: 12px;
            padding: 8px 14px;
            background: #2563eb;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            transition: 0.2s;
        }
        .btn:hover {
            background: #1d4ed8;
        }

        footer {
            background-color: #1f2937;
            color: #ccc;
            text-align: center;
            padding: 10px;
            font-size: 12px;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="dashboard">
        <!-- Sidebar toggle -->
        <button class="menu-btn" onclick="openSidebar()">☰ <div class="icon-label">MENU</div></button>
       

        <!-- Welcome -->
        <div>
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
            <p>Your client dashboard</p>
        </div>

        <!-- Top-right Icons -->
        <div class="top-icons">
            <div class="icon-box">
                <a href="notifications.php">🔔</a>
                <?php if ($new_notifs > 0) echo "<span class='badge'>$new_notifs</span>"; ?>
                <div class="icon-label">Notifications</div>
            </div>
            <div class="icon-box">
                <a href="messages.php">📩</a>
                <?php if ($unread_messages > 0) echo "<span class='badge'>$unread_messages</span>"; ?>
                <div class="icon-label">Messages</div>
            </div>
            <div class="icon-box">
                <a href="payment.php">💳</a>
                <div class="icon-label">Payments</div>
            </div>
            <div class="dropdown">
            <span class="icon">👤</span>
            <div class="icon-label">Profile</div>
            <div class="dropdown-content">
            <a href="profile.php">Edit Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
        </div>
    </div>

    <!-- SIDEBAR -->
    <div id="sidebar" class="sidebar">
        <span class="close-btn" onclick="closeSidebar()">×</span>
        <a href="view_classes.php">View Classes</a>
        <a href="view_payments.php">View My Payments</a>
        <a href="client_attendance.php">Attendance</a>
        <a href="feedback.php">Send Feedback</a>
        <a href="view_reportc.php">View Activity History</a>
    </div>

    <!-- INVENTORY -->
    <div class="section-header">
        <h1>Available Equipment</h1>
        <p>Here are some of the items available for use at the gym.</p>
    </div>
    <div class="inventory-grid">
        <?php while ($item = mysqli_fetch_assoc($result)) : ?>
        <div class="item-card">
            <?php if (!empty($item['image'])) : ?>
                <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
            <?php else : ?>
                <img src="images/d-bells.jpeg" alt="Default Inventory Image">
            <?php endif; ?>
            <div class="item-content">
                <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                <p><?php echo htmlspecialchars($item['description']); ?></p>
                <p><strong>Available:</strong> <?php echo $item['quantity']; ?></p>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <footer>
        <p>Contact us at fittrackke@gmail.com | +254771246544</p>
        <p>&copy; 2025 Fit Track. All rights reserved.</p>
    </footer>

    <script>
        function openSidebar() {
            document.getElementById("sidebar").style.width = "250px";
        }
        function closeSidebar() {
            document.getElementById("sidebar").style.width = "0";
        }
    </script>
</body>
</html>
