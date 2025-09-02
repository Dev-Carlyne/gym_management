<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection file
include 'db_connect.php';  // This makes $conn available

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and assign form data to variables
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $id_number = mysqli_real_escape_string($conn, $_POST['id_number']);
    $raw_password = $_POST['password']; // raw password for validation
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // 🔒 Password validation: at least 8 chars, 1 uppercase, 1 special character
    if (strlen($raw_password) < 8 || 
        !preg_match("/[A-Z]/", $raw_password) || 
        !preg_match("/[^a-zA-Z0-9]/", $raw_password)) {
        echo "❌ Password must be at least 8 characters long, contain at least one uppercase letter and one special symbol.";
        exit();
    }

    // Hash the password only after it passes validation
    $password = password_hash($raw_password, PASSWORD_DEFAULT);

    // Check if ID number already exists
    $check = "SELECT * FROM users WHERE id_number = '$id_number'";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) > 0) {
        echo "⚠️ ID number already exists. Please try again.";
    } else {
        // Insert the new user into the database
        $sql = "INSERT INTO users (name, email, id_number, password, role) 
                VALUES ('$name', '$email', '$id_number', '$password', '$role')";

        if (mysqli_query($conn, $sql)) {
            // Redirect to the login page
            header("Location: login.html");
            exit();
        } else {
            // Show error if the query fails
            echo "❌ Error: " . mysqli_error($conn);
        }
    }
}
?>
