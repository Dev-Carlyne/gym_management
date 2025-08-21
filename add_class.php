<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['class_name'];
    $trainer = $_POST['trainer'];
    $schedule = $_POST['schedule'];
    $duration = $_POST['duration'];
    $max = $_POST['max_participants'];

    $sql = "INSERT INTO classes (class_name, trainer, schedule, duration, max_participants) 
            VALUES ('$name', '$trainer', '$schedule', '$duration', $max)";

    if (mysqli_query($conn, $sql)) {
        header("Location: manage_classes.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Class</title>
    <style>
        body {
    margin: 0;
    padding: 0;
    background: url('images/1.jpeg') no-repeat center center fixed;
    background-size: cover;
    font-family: Arial, sans-serif;
    color: #fff;
    min-height: 100vh;
}

.overlay {
    background-color: rgba(0, 0, 0, 0.6); /* Dark overlay */
    min-height: 100vh;
    padding: 40px;
    padding-bottom: 100px;
}

/* Form styling */
form {
    max-width: 500px;
    margin: 0 auto;
    background-color: rgba(255, 255, 255, 0.1);
    padding: 20px;
    border-radius: 10px;
    backdrop-filter: blur(4px);
}

form label {
    display: block;
    margin-top: 10px;
    font-weight: bold;
}

form input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: none;
    border-radius: 5px;
    margin-bottom: 15px;
}

form button {
    background-color: #00c896;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
}

form button:hover {
    background-color: #00a07a;
}

/* Footer styling */
footer {
    position: fixed;
    bottom: 0;
    width: 100%;
    text-align: center;
    background-color: rgba(0, 0, 0, 0.7);
    padding: 15px;
    font-size: 14px;
    color: #fff;
}

    </style>
</head>
<body>
    <div style="text-align: center;">
    <a href="admin_dashboard.php" 
    style="
        display: inline-block;
        background-color: #2563eb;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        margin: 20px 0;
    ">← Back to Dashboard</a>
    </div>  

    <h1>Add New Class</h1>

    <form method="POST">
        <label>Class Name:</label>
        <input type="text" name="class_name" required><br>

        <label>Trainer:</label>
        <input type="text" name="trainer" required><br>

        <label>Schedule:</label>
        <input type="text" name="schedule" required><br>

        <label>Duration:</label>
        <input type="text" name="duration" required><br>

        <label>Max Participants:</label>
        <input type="number" name="max_participants" required><br>

        <button type="submit">Add Class</button>
    </form>
    <footer>
        <p>&copy; 2025 Fit Track. All rights reserved.</p>
    </footer>
</body>
</html>
