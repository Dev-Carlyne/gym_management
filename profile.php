<?php
include 'db_connect.php';
session_start();

// Always initialize status variables
$fatal_error = "";
$success_msg = "";
$error_msg   = "";

// --- Guard: must be logged in ---
if (!isset($_SESSION['user_id']) && !isset($_SESSION['id_number'])) {
    header("Location: login.php");
    exit();
}

// Determine the client's ID NUMBER (primary key)
$clientIdNumber = $_SESSION['id_number'] ?? $_SESSION['user_id'] ?? null;
// If your system stores something else in user_id, make sure it equals id_number.
// We treat it as a string to be safe (some IDs can have leading zeros/letters).
if ($clientIdNumber === null) {
    die("Cannot determine your account ID. Please log in again.");
}

// --- Fetch current user record by id_number ---
function fetch_user_by_id_number(mysqli $conn, $idNumber) {
    $sql = "SELECT id_number, name, phone, email FROM users WHERE id_number = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $idNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc() ?: null;
}

$user = fetch_user_by_id_number($conn, $clientIdNumber);

// If still not found, show a friendly message instead of throwing notices
if (!$user) {
    $fatal_error = "We couldn't find your profile using ID number '{$clientIdNumber}'. 
    If you recently changed your login logic, ensure the session stores the user's *id_number*.";
}

// --- Handle updates ---
$success_msg = $error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$fatal_error) {
    $name  = trim($_POST['name'] ?? "");
    $phone = trim($_POST['phone'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $pass  = trim($_POST['password'] ?? "");

    // Basic validation
    if ($name === "" || $phone === "" || $email === "") {
        $error_msg = "Please fill in name, phone and email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Please enter a valid email address.";
    } else {
        if ($pass !== "") {
            // Update with password
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $upd = $conn->prepare("UPDATE users SET name=?, phone=?, email=?, password=? WHERE id_number=?");
            $upd->bind_param("sssss", $name, $phone, $email, $hashed, $clientIdNumber);
        } else {
            // Update without password
            $upd = $conn->prepare("UPDATE users SET name=?, phone=?, email=? WHERE id_number=?");
            $upd->bind_param("ssss", $name, $phone, $email, $clientIdNumber);
        }

        if ($upd->execute()) {
            $success_msg = "Profile updated successfully!";
            // Refresh data
            $user = fetch_user_by_id_number($conn, $clientIdNumber);
            // Keep header in sync if you use $_SESSION['user_name']
            $_SESSION['user_name'] = $user['name'] ?? $_SESSION['user_name'];
        } else {
            $error_msg = "Error updating profile. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: url('images/1.jpeg') no-repeat center center fixed;
            background-size: cover; margin: 0; padding: 0; }
        .wrap { max-width: 560px; margin: 32px auto; padding: 24px; background: #fff;
                border-radius: 12px; box-shadow: 0 6px 12px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-top: 0; color: #1f2937; }
        .top-actions { display: flex; justify-content: space-between; margin-bottom: 12px; }
        .btn-link { text-decoration: none; background: #111827; color: #fff; padding: 8px 12px; border-radius: 8px; }
        label { display: block; margin-top: 12px; font-size: 14px; color: #374151; }
        input { width: 100%; padding: 10px; margin-top: 6px; border-radius: 6px; border: 1px solid #d1d5db; font-size: 14px; }
        input[readonly] { background: #f3f4f6; }
        button { width: 100%; padding: 12px; margin-top: 18px; background: #2563eb; color: #fff; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        .message { margin: 10px 0; text-align: center; font-size: 14px; }
        .success { color: #065f46; background: #d1fae5; padding: 8px; border-radius: 8px; }
        .error { color: #991b1b; background: #fee2e2; padding: 8px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="top-actions">
            <a class="btn-link" href="client_dashboard.php">⬅ Back to Dashboard</a>
        </div>
        <h2>My Profile</h2>

        <?php if (!empty($fatal_error)): ?>
            <p class="message error"><?php echo htmlspecialchars($fatal_error); ?></p>
        <?php else: ?>
            <?php if (!empty($success_msg)) echo "<p class='message success'>".htmlspecialchars($success_msg)."</p>"; ?>
            <?php if (!empty($error_msg))   echo "<p class='message error'>".htmlspecialchars($error_msg)."</p>"; ?>

            <form method="POST" novalidate>
                <label>ID Number (not editable)</label>
                <input type="text" value="<?php echo htmlspecialchars($user['id_number'] ?? ''); ?>" readonly>

                <label>Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>

                <label>Phone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>

                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>

                <label>Password (leave blank to keep current)</label>
                <input type="password" name="password" placeholder="Enter new password (optional)">

                <button type="submit">Update Profile</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
