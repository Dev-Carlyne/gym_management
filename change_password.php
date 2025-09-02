<?php
session_start();
include 'db_connect.php'; 

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_number = mysqli_real_escape_string($conn, $_POST['id_number']);
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate new password
    if (strlen($new_password) < 8 || 
        !preg_match('/[A-Z]/', $new_password) || 
        !preg_match('/[\W]/', $new_password)) {
        $message = "New password must be at least 8 characters, contain 1 uppercase letter and 1 symbol.";
    } elseif ($new_password !== $confirm_password) {
        $message = "New password and confirmation do not match.";
    } else {
        // Find user
        $sql = "SELECT * FROM users WHERE id_number = '$id_number' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($old_password, $user['password'])) {
                // Hash new password
                $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                $update = "UPDATE users SET password='$hashedPassword' WHERE id_number='$id_number'";

                if (mysqli_query($conn, $update)) {
                    $message = "Password updated successfully. Please login again.";
                } else {
                    $message = "Error updating password.";
                }
            } else {
                $message = "Old password is incorrect.";
            }
        } else {
            $message = "No user found with that ID Number.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password</title>
  <style>
    body { font-family: Arial, sans-serif;  background: url('images/1.jpeg') no-repeat center center fixed; background-size: cover; padding:50px; }
    .form-container { background-color: rgba(255,255,255,0.1); padding:20px; border-radius:10px; max-width:400px; margin:auto; box-shadow:0px 0px 10px rgba(0,0,0,0.1);}
    input { width:100%; padding:15px; margin:10px 0; border:1px solid #ccc; border-radius:5px;}
    .password-container { position: relative; }
    .toggle-password { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color:#555;}
    button { width:100%; padding:12px; background:#ff6b6b; color:#fff; border:none; border-radius:5px; cursor:pointer;}
    button:hover { background:#e85a5a; }
    p.message { text-align:center; color:red; }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Change Password</h2>
    <?php if ($message) echo "<p class='message'>$message</p>"; ?>
    <form method="POST">
      <input type="text" name="id_number" placeholder="ID Number" required>

      <div class="password-container">
        <input type="password" name="old_password" id="old_password" placeholder="Old Password" required>
        <span class="toggle-password" onclick="togglePassword('old_password')">👁</span>
      </div>

      <div class="password-container">
        <input type="password" name="new_password" id="new_password" placeholder="New Password" required>
        <small>Password must be at least 8 characters, include 1 uppercase letter and 1 symbol.</small>
        <span class="toggle-password" onclick="togglePassword('new_password')">👁</span>
      </div>

      <div class="password-container">
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" required>
        <small>Password must be at least 8 characters, include 1 uppercase letter and 1 symbol.</small>
        <span class="toggle-password" onclick="togglePassword('confirm_password')">👁</span>
        
      </div>

      <button type="submit">Update Password</button>
    </form>
    <p style="text-align:center; margin-top:10px;">
      <a href="login.html">Back to Login</a>
    </p>
  </div>

  <script>
    function togglePassword(id) {
      const field = document.getElementById(id);
      if (field.type === "password") {
        field.type = "text";
      } else {
        field.type = "password";
      }
    }
  </script>
</body>
</html>
