<?php
include 'db_connect.php';

$id = $_GET['id'];
$sql = "DELETE FROM inventory WHERE item_id = $id";
mysqli_query($conn, $sql);

header("Location: manage_inventory.php");
exit;
?>
