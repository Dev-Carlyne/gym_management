<?php
include 'db_connect.php';

$id = $_GET['id'];
$sql = "SELECT * FROM inventory WHERE item_id = $id";
$result = mysqli_query($conn, $sql);
$item = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];

    $update = "UPDATE inventory SET 
                item_name = '$item_name',
                description = '$description',
                quantity = $quantity
               WHERE item_id = $id";
    mysqli_query($conn, $update);
    header("Location: manage_inventory.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Inventory Item</title>
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
<div class="overlay">
    <h1>Edit Inventory Item</h1>

    <form method="POST" action="">
        <label>Item Name:</label><br>
        <input type="text" name="item_name" value="<?php echo htmlspecialchars($item['item_name']); ?>" required><br>

        <label>Description:</label><br>
        <textarea name="description"><?php echo htmlspecialchars($item['description']); ?></textarea><br>

        <label>Quantity:</label><br>
        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" required><br>

        <button type="submit">Update Item</button>
    </form>
</div>
<footer>
    <p>&copy; 2025 Fit Track. All rights reserved.</p>
</footer>
</body>
</html>
