<?php
include 'db_connect.php';

$id = $_GET['id'];

$sql = "DELETE FROM users WHERE id = $id";

if (mysqli_query($conn, $sql)) {
    header("Location: view_users.php");
    exit;
} else {
    echo "Error deleting user: " . mysqli_error($conn);
}
?>
