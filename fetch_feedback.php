<?php
include 'db_connect.php';

$sql = "SELECT message, submitted_at FROM feedback ORDER BY submitted_at DESC LIMIT 5";
$result = mysqli_query($conn, $sql);

$feedbacks = [];
while ($row = mysqli_fetch_assoc($result)) {
    $feedbacks[] = $row;
}

header('Content-Type: application/json');
echo json_encode($feedbacks);
?>
