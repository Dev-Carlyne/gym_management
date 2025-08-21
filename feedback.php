<?php
include 'db_connect.php';
session_start();

$id_number = isset($_SESSION['id_number']) ? $_SESSION['id_number'] : null;
$message = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    if (!empty($message)) {
        $id_column = $id_number ? "'$id_number'" : "NULL";
        $sql = "INSERT INTO feedback (id_number, message) VALUES ($id_column, '$message')";
        if (mysqli_query($conn, $sql)) {
            $success = "✅ Feedback submitted successfully!";
            $message = ""; // Clear the textarea
        } else {
            $success = "❌ Error submitting feedback: " . mysqli_error($conn);
        }
    } else {
        $success = "⚠️ Please enter a message.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Feedback</title>
    <meta charset="UTF-8">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('images/1.jpeg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }

        h1 {
            text-align: center;
            padding: 30px 10px 10px;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
        }

        .btn {
            display: inline-block;
            background-color: #ff6b6b;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            margin-top: 15px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #e85a5a;
        }

        .status {
            text-align: center;
            margin: 20px auto;
            font-weight: bold;
        }

        footer {
            background-color: rgba(0,0,0,0.8);
            text-align: center;
            color: #ccc;
            padding: 15px;
            bottom: 0;
            width: 100%;
        }

        a.btn {
            display: block;
            text-align: center;
            width: fit-content;
            margin: 20px auto 0;
        }
    </style>
</head>
<body>
   
    <h1>Submit Feedback</h1>

    <?php if (!empty($success)) { echo "<p class='status'>$success</p>"; } ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="message">Your Feedback:</label>
            <textarea name="message" rows="6" required><?php echo htmlspecialchars($message); ?></textarea>
        </div>
        <button type="submit" class="btn">Submit</button>
    </form>

    <div style="text-align: center;">
        <a href="client_dashboard.php" 
        style="
        display: inline-block;
        background-color: #2563eb;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        margin: 20px 0;
    ">← Back to Dashboard</a>

    <footer>
        <p>&copy; 2025 Fit Track. All rights reserved.</p>
    </footer>
</body>
</html>
