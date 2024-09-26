<?php
session_start(); 
require 'config.php'; 

error_reporting(E_ALL);
ini_set('display_errors', 1);


$stmt = $conn->prepare("SELECT id, username, email FROM users WHERE role = 'user'");
if (!$stmt) {
    die("Database prepare error: " . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
        }
        .sidebar {
            background-color: #333;
            color: #fff;
            width: 250px;
            padding: 20px;
            box-sizing: border-box;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px 0;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .sidebar a:hover {
            background-color: #575757;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            box-sizing: border-box;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f9;
        }
        .action-button {
            background-color: #5bc0de;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 5px;
        }
        .action-button:hover {
            background-color: #31b0d5;
        }
        .deactivate-button {
            background-color: #d9534f;
        }
        .deactivate-button:hover {
            background-color: #c9302c;
        }
        .reset-password-button {
            background-color: #f0ad4e;
        }
        .reset-password-button:hover {
            background-color: #ec971f;
        }
        @media (max-width: 600px) {
            .sidebar,
        .main-content {
        width: 100%;
        padding: 15px;
    }
}
    </style>
</head>
<body>

<div class="sidebar">
    <h3>Admin Menu</h3>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <h2>Admin Dashboard</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                            <input type="submit" name="deactivate" value="Deactivate" class="action-button deactivate-button">
                        </form>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                            <input type="submit" name="reset_password" value="Reset Password" class="action-button reset-password-button">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
