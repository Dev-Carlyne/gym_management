<?php
session_start();

// Include database connection
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_number = mysqli_real_escape_string($conn, $_POST['id_number']);
    $password = $_POST['password'];

    // ✅ Password complexity validation (to give consistent feedback)
    if (strlen($password) < 8 || 
        !preg_match('/[A-Z]/', $password) || 
        !preg_match('/[^a-zA-Z0-9]/', $password)) {
        echo "<p style='color:red; text-align:center;'>Password must be at least 8 characters, 
              contain an uppercase letter and a symbol.</p>";
        exit();
    }

    // Query to fetch user data
    $sql = "SELECT * FROM users WHERE id_number = '$id_number' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            // Store user info in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['id_number'] = $user['id_number'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
                exit();
            } else {
                header("Location: client_dashboard.php");
                exit();
            }
        } else {
            echo "<p style='color:red; text-align:center;'>Invalid password.</p>";
        }
    } else {
        echo "<p style='color:red; text-align:center;'>No user found with that ID number.</p>";
    }
}
?>
