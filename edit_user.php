<?php
include 'db_connect.php';

$id = $_GET['id'];
$sql = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $update = "UPDATE users SET 
                name = '$full_name',
                email = '$email',
                role = '$role'
               WHERE id = $id";

    if (mysqli_query($conn, $update)) {
        header("Location: view_users.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('images/1.jpeg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }

        h1 {
            text-align: center;
            color: white;
            margin-top: 30px;
        }

        form {
            background-color: rgba(255, 255, 255, 0.95);
            width: 90%;
            max-width: 500px;
            margin: 40px auto;
            padding: 30px;
            border-radius: 10px;
        }

        label {
            display: block;
            margin-top: 15px;
        }

        input[type="text"],
        input[type="email"],
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #555;
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
    </style>
</head>
<body>

    <h1>Edit User</h1>

    <form method="POST">
        <label>Full Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label>Role:</label>
        <select name="role" required>
            <option value="client" <?php if ($user['role'] == 'client') echo 'selected'; ?>>Client</option>
            <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
        </select>

        <button type="submit">Update User</button>
    </form>

    <footer>
        <p>&copy; 2025 Fit Track. All rights reserved.</p>
    </footer>

</body>
</html>
