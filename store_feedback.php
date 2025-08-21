<?php
// store_feedback.php

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $message = $_POST['message'] ?? '';
    $rating = $_POST['rating'] ?? null;
    $source = $_POST['source'] ?? 'client'; // fallback

    $id_number = $source === 'public' ? 'PUBLIC' : ($_SESSION['id_number'] ?? '');

    include 'db_connect.php'; 

    $stmt = $conn->prepare("INSERT INTO feedback (id_number, message, rating, submitted_at, source) VALUES (?, ?, ?, NOW(), ?)");
    $stmt->bind_param("ssis", $id_number, $message, $rating, $source);
    $stmt->execute();


    $stmt->close();
    $conn->close();

    // Redirect back to index.html with thank you message (optional)
    header("Location: index.html");
    exit();
}
