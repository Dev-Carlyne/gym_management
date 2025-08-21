<?php
include 'db_connect.php';
session_start();

// Check if the user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    // Redirect to login if not logged in or not a client
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM inventory WHERE quantity > 0";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <style>
        /* General Body Styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
         background: url('images/1.jpeg') no-repeat center center fixed;
         background-size: cover;
         color: #333;
         min-height: 100vh;
         display: flex;
         flex-direction: column;
        }

/* Dashboard Header */
.dashboard {
    background-color: rgba(31, 41, 55, 0.9); /* Slight transparency */
    color: #fff;
    padding: 30px 20px;
    text-align: center; 
}

.dashboard h1 {
    margin: 0;
    font-size: 32px;
}

.dashboard p {
    font-size: 16px;
    margin-top: 10px;
}

/* Navigation Menu */
nav ul {
    list-style: none;
    padding: 0;
    margin-top: 20px;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
}

nav ul li {
    display: inline-block;
}

nav ul li a {
    text-decoration: none;
    color: #fff;
    background-color: #2563eb;
    padding: 10px 20px;
    border-radius: 8px;
    transition: background-color 0.3s;
}

nav ul li a:hover {
    background-color: #1e40af;
}

/* Welcome Section */
div > h1 {
    text-align: center;
    margin-top: 30px;
    font-size: 28px;
}

div > p {
    text-align: center;
    font-size: 16px;
    margin-bottom: 40px;
}

/* Class Gallery Section */
.gallery-container {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 20px;
    padding: 0 20px 40px;
}

.gallery-card {
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    width: 320px;
    text-align: center;
    transition: transform 0.2s ease-in-out;
}

.gallery-card:hover {
    transform: translateY(-5px);
}

.gallery-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.card-info {
    padding: 20px;
}

.card-info h2 {
    font-size: 20px;
    margin-bottom: 10px;
    color: #111827;
}

.card-info p {
    font-size: 14px;
    color: #4b5563;
    margin-bottom: 20px;
}

.card-info button {
    background-color: #10b981;
    color: #fff;
    border: none;
    padding: 10px 16px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.card-info button:hover {
    background-color: #059669;
}

/* Footer */
footer {
    background-color: #1f2937;
    color: #ccc;
    text-align: center;
    padding: 15px 0;
    font-size: 14px;
    width: 100%;
    bottom: 0;
    left: 0;
}

    </style>

</head>
<body>
    <div class="dashboard">
        <h1>Welcome, Client!</h1>
        <p>You are logged in as a client.</p>
        
        <!-- Add client-related content or links here -->
        <nav>
            <ul>
                <li><a href="view_classes.php">View Classes</a></li>
                <li><a href="payment.php">Make Payment</a></li>
                <li><a href="view_payments.php">View My Payments</a></li>
                <li><a href="view_reportc.php">View Activity History</a></li>
                <li><a href="feedback.php">Send Feedback</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
    
    <div>
        <h1 style="text-align: center;background-color: #ffffff; margin-top: 40px;">Available Equipment</h1><br>
        <p style="text-align:center; background-color: #ffffff;">Here are some of the items available for use at the gym.</p><br>
    </div>

    <div class="gallery-container">
        <?php while ($item = mysqli_fetch_assoc($result)) : ?>
        <div class="gallery-card">
            <?php if (!empty($item['image'])) : ?>
                <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
            <?php else : ?>
                <img src="images/d-bells.jpeg" alt="Default Inventory Image">
            <?php endif; ?>
            <div class="card-info">
                <h2><?php echo htmlspecialchars($item['item_name']); ?></h2>
                <p><?php echo htmlspecialchars($item['description']); ?></p>
                <p><strong>Available:</strong> <?php echo $item['quantity']; ?></p>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <footer>
        <p>&copy; 2025 Fit Track. All rights reserved.</p>
    </footer>
</body>
</html>
