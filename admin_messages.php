<?php
include 'db_connect.php';
session_start();

// Ensure only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['user_id'];

// Handle reply
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reply_message'])) {
    $receiver_id = intval($_POST['receiver_id']); // client id
    $message = trim($_POST['reply_message']);

    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, subject, message) VALUES (?, ?, ?, ?)");
        $subject = "Reply from Admin";
        $stmt->bind_param("iiss", $admin_id, $receiver_id, $subject, $message);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch all messages (grouped by client)
$sql = "SELECT m.*, u.name AS sender_name
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE m.receiver_id = ?
        OR m.sender_id = ?
        ORDER BY m.created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $admin_id, $admin_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[$row['sender_id']][] = $row; // group by client
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Messages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('images/1.jpeg') no-repeat center center fixed;
            background-size: cover;
            padding: 20px;
        }
        .chat-box {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            max-width: 600px;
        }
        .message {
            padding: 8px;
            margin: 6px 0;
            border-radius: 8px;
        }
        .client {
            background: #e0f7fa;
            text-align: left;
        }
        .admin {
            background: #c8e6c9;
            text-align: right;
        }
        form {
            margin-top: 10px;
            text-align: right;
        }
        textarea {
            width: 100%;
            height: 50px;
            border-radius: 6px;
            padding: 8px;
        }
        button {
            background: #00796b;
            color: #fff;
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 5px;
        }
        
    </style>
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
</head>
<body>

<h2 style="color:white;">Admin Messages</h2>

<?php foreach ($messages as $client_id => $chat): ?>
    <div class="chat-box">
        <h3>Conversation with <?= htmlspecialchars($chat[0]['sender_name']) ?></h3>
        <?php foreach ($chat as $msg): ?>
            <div class="message <?= $msg['sender_id'] == $admin_id ? 'admin' : 'client' ?>">
                <strong><?= $msg['sender_id'] == $admin_id ? 'Admin' : $msg['sender_name'] ?>:</strong>
                <?= htmlspecialchars($msg['message']) ?><br>
                <small><?= $msg['created_at'] ?></small>
            </div>
        <?php endforeach; ?>

        <!-- Reply Form -->
        <form method="POST">
            <input type="hidden" name="receiver_id" value="<?= $client_id ?>">
            <textarea name="reply_message" placeholder="Type your reply..."></textarea><br>
            <button type="submit">Reply</button>
        </form>
    </div>
<?php endforeach; ?>
<footer>
         <p>&copy; 2025 Fit Track. All rights reserved.</p>
    </footer>
</body>
</html>
