<?php
include 'db_connect.php';

$id = $_GET['id'];

$sql = "DELETE FROM classes WHERE class_id = $id";

if (mysqli_query($conn, $sql)) {
    header("Location: manage_classes.php");
    exit;
} else {
    echo "Error deleting class: " . mysqli_error($conn);
}
?>
