<?php
// Connect to the database
include 'db_connect.php'; // or use your existing connection file

// Fetch all classes
$sql = "SELECT * FROM classes";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Classes</title>
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
    <h1>Manage Gym Classes</h1>

    <a href="add_class.php" class="btn">Add New Class</a>

    <table>
        <thead>
            <tr>
                <th>Class ID</th>
                <th>Class Name</th>
                <th>Trainer</th>
                <th>Schedule</th>
                <th>Duration</th>
                <th>Max Participants</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['class_id']; ?></td>
                        <td><?php echo $row['class_name']; ?></td>
                        <td><?php echo $row['trainer']; ?></td>
                        <td><?php echo $row['schedule']; ?></td>
                        <td><?php echo $row['duration']; ?></td>
                        <td><?php echo $row['max_participants']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td>
                            <a href="edit_class.php?id=<?php echo $row['class_id']; ?>">Edit</a> |
                            <a href="delete_class.php?id=<?php echo $row['class_id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8">No classes found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<footer>
    <p>&copy; 2025 Fit Track. All rights reserved.</p>
</footer>
</body>
</html>
