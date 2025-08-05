<?php
session_start();
include 'db_connect.php';

$sql = "SELECT * FROM classes ORDER BY class_id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Classes</title>
    <style>
        /* Background image and dark overlay */
body {
    margin: 0;
    padding: 0;
    background: url('images/1.jpeg') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    position: relative;
    min-height: 100vh;
    color: #fff;
}

.overlay {
    background-color: rgba(0, 0, 0, 0.6); /* Dark overlay */
    min-height: 100vh;
    padding: 20px;
    padding-bottom: 80px; /* Room for footer */
}

/* Card layout */
.container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}

.card {
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 20px;
    width: 300px;
    backdrop-filter: blur(5px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
    color: #fff;
}

.card h2 {
    margin-top: 0;
}

.card button {
    background-color: #ff6b6b;
    color: #fff;
    border: none;
    padding: 10px 20px;
    margin-top: 10px;
    cursor: pointer;
    border-radius: 5px;
}

.card button:hover {
    background-color: #ff3b3b;
}

/* Fixed footer */
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

    <h1>Available Classes</h1>

    <div class="container">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="card">
                <h2><?php echo htmlspecialchars($row['class_name']); ?></h2>
                <p><strong>Instructor:</strong> <?php echo htmlspecialchars($row['trainer']); ?></p>
                <p><strong>Schedule:</strong> <?php echo htmlspecialchars($row['schedule']); ?></p>
                <p><strong>Max Participants:</strong> <?php echo htmlspecialchars($row['max_participants']); ?></p>
                <form action="book_class.php" method="POST">
                    <input type="hidden" name="id_number" value="<?php echo $id_number; ?>">
                    <input type="hidden" name="class_id" value="<?php echo $row['class_id']; ?>">
                    <button type="submit">Book</button>
                </form>

            </div>
        <?php } ?>
    </div>
    <footer>
        <p>&copy; 2025 Fit Track. All rights reserved.</p>
    </footer>
</body>
</html>
