<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: url('images/1.jpeg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: -1;
        }

        header {
            background-color: #111;
            padding: 20px;
            text-align: center;
        }

        header h1 {
            color: #fff;
            margin: 0;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 25px;
            color: #333;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: scale(1.03);
        }

        .card h2 {
            margin-top: 0;
            font-size: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .action {
            margin-top: 15px;
        }

        .btn {
            background-color: #111;
            color: #fff;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }

        .btn:hover {
            background-color: #333;
        }

        footer {
            background-color: #111;
            color: white;
            text-align: center;
            padding: 15px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        @media screen and (max-width: 600px) {
            .container {
                padding: 20px;
            }

            .card h2 {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
    </header>

    <div class="container">
        <div class="card">
            <h2><i class="fas fa-user-cog"></i> Manage Users</h2>
            <p>Register, monitor, and manage all system users. Ensure accounts are secure and active.</p>
            <div class="action">
                <a href="view_users.php" class="btn">View Users</a>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-chart-line"></i> Reports</h2>
            <p>Generate detailed reports on attendance, payments, and user activity to monitor gym performance.</p>
            <div class="action">
                <a href="view_reports.php" class="btn">Reports</a>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-dumbbell"></i> Inventory</h2>
            <p>Track and maintain all gym equipment. Add new items, update conditions, and retire old inventory.</p>
            <div class="action">
                <a href="manage_inventory.php" class="btn">Inventory</a>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-calendar-alt"></i> Classes</h2>
            <p>Control class scheduling, assign trainers, and monitor participation for all fitness programs.</p>
            <div class="action">
                <a href="manage_classes.php" class="btn">Classes</a>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-dumbbell"></i> Messages</h2>
            <p>Track and reply to all messages sent by the client.</p>
            <div class="action">
                <a href="admin_messages.php" class="btn">Messages</a>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-calendar-alt"></i> Post Notifications</h2>
            <p>Post news, updates and upcoming classes to the clients.</p>
            <div class="action">
                <a href="post_notification.php" class="btn">Post Notifications</a>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-sign-out-alt"></i> Log Out</h2>
            <p>Click below to securely log out of your admin account.</p>
            <div class="action">
                <a href="logout.php" class="btn">Log Out</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Fit Track. All rights reserved.</p>
    </footer>
</body>
</html>
