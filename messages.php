<?php
session_start();
include 'db_connect.php';

// 🔹 Set admin_id (the admin has user_id = 2)
$admin_id = 2;
$current_user_id = $_SESSION['user_id'];

// 🔹 If client is logged in, receiver is always admin
if (isset($_POST['message'])) {
    $receiver_id = ($current_user_id == $admin_id) ? $_POST['receiver_id'] : $admin_id;
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $current_user_id, $receiver_id, $subject, $message);
    $stmt->execute();
}

// 🔹 Determine conversation partner
if ($current_user_id == $admin_id) {
    // Admin selects which client thread to view
    $client_id = $_GET['client_id'] ?? 0;
} else {
    // Client only chats with admin
    $client_id = $current_user_id;
}

// 🔹 Fetch thread
$sql = "SELECT m.*, u.name AS sender_name
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE (m.sender_id = ? AND m.receiver_id = ?)
           OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.created_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $client_id, $admin_id, $admin_id, $client_id);
$stmt->execute();
$messages = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Messages</title>
</head>
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
<body style="margin:0; font-family: Arial, sans-serif; background: url('images/1.jpeg') no-repeat center center fixed; background-size: cover;">

<div style="background-color: rgba(255,255,255,0.9); width: 60%; margin: 30px auto; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
    <h2 style="text-align:center; margin-bottom: 20px;">📩 Messages</h2>

    <div style="max-height:400px; overflow-y:auto; border:1px solid #ccc; padding:10px; border-radius:8px; background:#fafafa;">
        <?php while ($row = $messages->fetch_assoc()): ?>
            <div style="margin-bottom:12px; <?php echo ($row['sender_id'] == $current_user_id) ? 'text-align:right;' : 'text-align:left;'; ?>">
                <div style="display:inline-block; padding:8px 12px; border-radius:12px; <?php echo ($row['sender_id'] == $current_user_id) ? 'background:#007BFF; color:white;' : 'background:#e9ecef;'; ?>">
                    <b><?php echo htmlspecialchars($row['sender_name']); ?>:</b> 
                    <?php echo htmlspecialchars($row['message']); ?>
                </div>
                <div style="font-size:12px; color:#555;"><?php echo $row['created_at']; ?></div>
            </div>
        <?php endwhile; ?>
    </div>

    <form method="POST" style="margin-top:20px; display:flex; flex-direction:column; gap:10px;">
        <?php if ($current_user_id == $admin_id): ?>
            <input type="hidden" name="receiver_id" value="<?php echo $client_id; ?>">
        <?php endif; ?>

        <input type="text" name="subject" placeholder="Subject" style="padding:8px; border-radius:8px; border:1px solid #ccc;">
        <textarea name="message" placeholder="Type your message..." required style="padding:10px; border-radius:8px; border:1px solid #ccc; height:80px;"></textarea>
        <button type="submit" style="background:#007BFF; color:white; padding:10px; border:none; border-radius:8px; cursor:pointer;">Send</button>
    </form>
</div>

</body>
</html>
