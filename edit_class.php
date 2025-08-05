<?php
include 'db_connect.php';

$id = $_GET['id'];
$sql = "SELECT * FROM classes WHERE class_id = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['class_name'];
    $trainer = $_POST['trainer'];
    $schedule = $_POST['schedule'];
    $duration = $_POST['duration'];
    $max = $_POST['max_participants'];

    $update = "UPDATE classes SET 
        class_name = '$name',
        trainer = '$trainer',
        schedule = '$schedule',
        duration = '$duration',
        max_participants = $max 
        WHERE class_id = $id";

    if (mysqli_query($conn, $update)) {
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
    <title>Edit Class</title>
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
            background-color: rgba(0, 0, 0, 0.6); /* dark overlay */
            min-height: 100vh;
            padding: 40px;
            padding-bottom: 100px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

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
            width: 100%;
        }

        form button:hover {
            background-color: #00a07a;
        }

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

    <h1>Edit Class</h1>

    <form method="POST">
        <label>Class Name:</label>
        <input type="text" name="class_name" value="<?php echo $row['class_name']; ?>" required><br>

        <label>Trainer:</label>
        <input type="text" name="trainer" value="<?php echo $row['trainer']; ?>" required><br>

        <label>Schedule:</label>
        <input type="text" name="schedule" value="<?php echo $row['schedule']; ?>" required><br>

        <label>Duration:</label>
        <input type="text" name="duration" value="<?php echo $row['duration']; ?>" required><br>

        <label>Max Participants:</label>
        <input type="number" name="max_participants" value="<?php echo $row['max_participants']; ?>" required><br>

        <button type="submit">Update Class</button>
    </form>
    <footer>
        <p>&copy; 2025 Fit Track. All rights reserved.</p>
    </footer>
</body>
</html>
