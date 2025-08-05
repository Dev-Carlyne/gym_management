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
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash the password
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Check if ID number already exists
    $check = "SELECT * FROM users WHERE id_number = '$id_number'";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) > 0) {
        echo "ID number already exists. Please try again.";
    } else {
        // Insert the new user into the database
        $sql = "INSERT INTO users (name, email, id_number, password, role) 
                VALUES ('$name', '$email', '$id_number', '$password', '$role')";

        if (mysqli_query($conn, $sql)) {
            // Redirect to the login page
            header("Location: ../login.html");
            exit();
        } else {
            // Show error if the query fails
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>
