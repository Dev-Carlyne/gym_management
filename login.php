<?php
session_start();

// Include database connection
include 'db_connect.php';  // Make sure this file exists and connects to your database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_number = mysqli_real_escape_string($conn, $_POST['id_number']);
    $password = $_POST['password'];

    // Query to fetch user data from the database
    $sql = "SELECT * FROM users WHERE id_number = '$id_number' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            // Store user information in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['id_number'] = $user['id_number'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on user role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
                exit();
            } else {
                header("Location: client_dashboard.php");
                exit();
            }
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that ID number.";
    }
}
?>
