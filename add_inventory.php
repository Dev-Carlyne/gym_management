<?php
include 'db_connect.php';

$image = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $targetDir = "images/";
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif','image/jpg'];
    $maxFileSize = 2 * 1024 * 1024; // 2MB

    $image = basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $image;
    $fileType = mime_content_type($_FILES["image"]["tmp_name"]);
    $fileSize = $_FILES["image"]["size"];

    if (!in_array($fileType, $allowedTypes)) {
        die("Error: Only JPEG, PNG, GIF, and JPG files are allowed.");
    }

    if ($fileSize > $maxFileSize) {
        die("Error: File size exceeds the 2MB limit.");
    }

    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        die("Error: Failed to upload the file.");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];

    $sql = "INSERT INTO inventory (item_name, description, quantity, image) 
      VALUES ('$item_name', '$description', $quantity, '$image')";
    mysqli_query($conn, $sql);
    header("Location: manage_inventory.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Inventory Item</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('images/1.jpeg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            color: #fff;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.6);
            min-height: 100vh;
            padding: 40px;
            padding-bottom: 100px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        form {
            width: 50%;
            margin: auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            backdrop-filter: blur(5px);
        }

        label {
            font-weight: bold;
        }

        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: none;
            border-radius: 5px;
        }

        input, textarea {
            background-color: rgba(255, 255, 255, 0.8);
            color: #000;
        }

        button {
            background-color: #00c896;
            color: white;
            cursor: pointer;
        }

        button:hover {
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
<div class="overlay">
    <h1>Add Inventory Item</h1>

    <form method="POST" action="" enctype="multipart/form-data">
        <label>Item Name:</label><br>
        <input type="text" name="item_name" required><br>

        <label>Description:</label><br>
        <textarea name="description"></textarea><br>

        <label>Quantity:</label><br>
        <input type="number" name="quantity" required><br>
        
        <label>Image:</label>
        <input type="file" name="image"><br>

        <button type="submit">Add Item</button>
    </form>
</div>
<footer>
    <p>&copy; 2025 Fit Track. All rights reserved.</p>
</footer>
</body>
</html>
