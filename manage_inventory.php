<?php
include 'db_connect.php';

$sql = "SELECT * FROM inventory ORDER BY item_id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Inventory</title>
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

        .btn {
            display: block;
            width: max-content;
            margin: 0 auto 20px auto;
            padding: 10px 20px;
            background-color: #00c896;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #00a07a;
        }

        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: center;
            color: #fff;
        }

        th {
            background-color: rgba(0, 0, 0, 0.5);
        }

        a {
            color: #00c896;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
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
    <h1>Inventory Management</h1>

    <a href="add_inventory.php" class="btn">+ Add New Item</a>
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

    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Image</th>
                <th>Added On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td>
                        <a href="edit_inventory.php?id=<?php echo $row['item_id']; ?>">Edit</a> |
                        <a href="delete_inventory.php?id=<?php echo $row['item_id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<footer>
    <p>&copy; 2025 Fit Track. All rights reserved.</p>
</footer>
</body>
</html>
