<?php
include 'db_connect.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle new notification submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_notification'])) {
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);

    if (!empty($title) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO notifications (title, message) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $message);
        $stmt->execute();
        $stmt->close();
        $success = "Notification posted successfully!";
    } else {
        $error = "All fields are required.";
    }
}

// Handle deactivation (hide notification)
if (isset($_GET['deactivate'])) {
    $id = intval($_GET['deactivate']);
    $stmt = $conn->prepare("UPDATE notifications SET is_active = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: post_notifications.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM notifications WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: post_notifications.php");
    exit();
}

// Fetch all notifications
$sql = "SELECT * FROM notifications ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notifications</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: url('images/1.jpeg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Form */
        form {
            margin-bottom: 30px;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 15px;
            background: #2563eb;
            color: #fff;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #1e40af;
        }

        /* Notifications list */
        .notification {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .notification h2 {
            margin: 0;
            font-size: 18px;
        }
        .notification p {
            margin: 5px 0;
        }
        .notification small {
            color: #666;
        }
        .actions {
            margin-top: 10px;
        }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: #2563eb;
            font-weight: bold;
        }
        .actions a:hover {
            color: #1e40af;
        }
        .msg {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            text-align: center;
        }
        .success { background: #d1fae5; color: #065f46; }
        .error { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="container">
    <div class="top-actions">
            <a class="btn-link" href="client_dashboard.php">⬅ Back to Dashboard</a>
        </div>
        <h1>Manage Notifications</h1>

        <?php if (!empty($success)): ?>
            <div class="msg success"><?php echo $success; ?></div>
        <?php elseif (!empty($error)): ?>
            <div class="msg error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Add Notification Form -->
        <form method="POST">
            <label for="title">Title</label>
            <input type="text" name="title" required>

            <label for="message">Message</label>
            <textarea name="message" rows="4" required></textarea>

            <button type="submit" name="add_notification">Post Notification</button>
        </form>

        <h2>All Notifications</h2>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($notif = mysqli_fetch_assoc($result)): ?>
                <div class="notification">
                    <h2><?php echo htmlspecialchars($notif['title']); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($notif['message'])); ?></p>
                    <small>Posted on <?php echo date("F j, Y, g:i a", strtotime($notif['created_at'])); ?></small><br>
                    <small>Status: <?php echo $notif['is_active'] ? "Active" : "Inactive"; ?></small>
                    <div class="actions">
                        <?php if ($notif['is_active']): ?>
                            <a href="?deactivate=<?php echo $notif['id']; ?>">Deactivate</a>
                        <?php endif; ?>
                        <a href="?delete=<?php echo $notif['id']; ?>" onclick="return confirm('Delete this notification?');">Delete</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No notifications yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
