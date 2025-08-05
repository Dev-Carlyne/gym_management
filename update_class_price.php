<?php
include 'db_connect.php';

if (isset($_POST['update_price'])) {
    $class_id = $_POST['class_id'];
    $new_price = $_POST['new_price'];

    $update = mysqli_query($conn, "UPDATE classes SET price = '$new_price' WHERE class_id = '$class_id'");

    if ($update) {
        $message = "Price updated successfully.";
    } else {
        $message = "Error updating price: " . mysqli_error($conn);
    }
}

$classes = mysqli_query($conn, "SELECT * FROM classes");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Class Prices</title>
    <link rel="stylesheet" href="../css/styles.css"> <!-- your existing CSS -->
</head>
<body>
    <div class="container">
     <h2>Update Class Prices</h2>

        <?php if (isset($message)) echo "<p>$message</p>"; ?>

     <table>
        <thead>
            <tr>
                <th>Class Name</th>
                <th>Current Price</th>
                <th>New Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($classes)) { ?>
                <tr>
                    <form method="POST" action="">
                        <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                        <td>Ksh <?php echo htmlspecialchars($row['price']); ?></td>
                        <td>
                            <input type="number" name="new_price" min="0" step="0.01" required>
                            <input type="hidden" name="class_id" value="<?php echo $row['class_id']; ?>">
                        </td>
                        <td>
                            <button type="submit" name="update_price">Update</button>
                        </td>
                    </form>
                </tr>
            <?php } ?>
        </tbody>
        </table>

        <br>
     <a href="../admin_dashboard.php">Back to Dashboard</a>
    </div>
    
    <footer>
        <p>&copy; 2025 Fit Track. All rights reserved.</p>
    </footer>
</body>
</html>
