<?php
include 'db_connect.php';
session_start();

// Check if the user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit();
}

// Fetch active notifications
$sql = "SELECT * FROM notifications WHERE is_active = 1 ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Notifications</title>
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

        .container {
            max-width: 900px;
            margin: 40px auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        h1 {
            text-align: center;
            color: #1f2937;
            margin-bottom: 20px;
        }

        .notification {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 6px solid #2563eb;
            transition: transform 0.2s;
        }
        .notification:hover {
            transform: translateX(5px);
        }

        .notification h2 {
            margin: 0;
            font-size: 20px;
            color: #111827;
        }
        .notification p {
            margin: 8px 0 0;
            color: #374151;
        }
        .notification small {
            display: block;
            margin-top: 8px;
            font-size: 12px;
            color: #6b7280;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            background: #2563eb;
            color: #fff;
            padding: 10px 18px;
            border-radius: 6px;
            transition: background 0.3s;
        }
        .back-link:hover {
            background: #1e40af;
        }

        footer {
            margin-top: auto;
            background: #1f2937;
            color: #ccc;
            text-align: center;
            padding: 10px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📢 Gym Announcements</h1>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($notif = mysqli_fetch_assoc($result)): ?>
                <div class="notification">
                    <h2><?php echo htmlspecialchars($notif['title']); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($notif['message'])); ?></p>
                    <small>Posted on <?php echo date("F j, Y, g:i a", strtotime($notif['created_at'])); ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center;">No announcements at the moment.</p>
        <?php endif; ?>

        <a href="client_dashboard.php" class="back-link">⬅ Back to Dashboard</a>
    </div>

    <footer>
        <p>&copy; 2025 Fit Track. All rights reserved.</p>
    </footer>
</body>
</html>
