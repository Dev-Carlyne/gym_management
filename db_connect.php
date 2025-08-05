<?php
$servername = "localhost";
$username = "root";  // Default username for MySQL in XAMPP
$password = "";  // Default password for MySQL in XAMPP (blank)
$database = "gg_management";  // Updated to gg_management

// Establishing the connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
