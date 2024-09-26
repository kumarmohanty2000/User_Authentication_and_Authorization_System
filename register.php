<?php

require 'config.php'; 

$usernameErr = $emailErr = $passwordErr = "";
$username = $email = $password =  "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = trim($_POST["username"]);
        if (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
            $usernameErr = "Only letters and numbers allowed";
        }
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = trim($_POST["password"]);
        if (strlen($password) < 6) {
            $passwordErr = "Password must be at least 6 characters";
        }
    }

    if (empty($usernameErr) && empty($emailErr) && empty($passwordErr)) {
        $checkDuplicate = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $checkDuplicate->bind_param("ss", $username, $email);
        $checkDuplicate->execute();
        $result = $checkDuplicate->get_result();

        if ($result->num_rows > 0) {
            $duplicateErr = "Username or Email already exists. Please choose another.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $successMessage = "Registration successful! You can now log in.";
                $username = $email = $password = ""; 
            } else {
                echo "<div class='error'>Error: " . $stmt->error . "</div>";
            }

            $stmt->close();
        }

        $checkDuplicate->close();
    }

    $conn->close();
       
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        a{
            text-decoration: none;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #0000001a;
            width: 300px;
        }
        .form-container h2 {
            margin-bottom: 20px;
        }
        .form-container input,
        .form-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .form-container button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        .form-container button:hover {
            background-color: #218838;
        }
        .success{
            margin-bottom: 20px;
            background-color: #73fa71;
            color: white;
            border: 2px solid #28a745 ;

        }
        .login{
            width: 100%;
            margin-top: 20px;
        }
        .login-button{
            background-color: chocolate;
            margin-top: auto;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 40%;

        }

    </style>
</head>
<body>

<div class="form-container">
    <h2>Register</h2>
    <?php if (!empty($duplicateErr)) : ?>
        <div class="error"><?php echo $duplicateErr; ?></div>
    <?php endif; ?>
    <?php if (!empty($successMessage)) : ?>
        <div class="success"><?php echo $successMessage; ?></div>
        <a href="login.php" class="login-button">Login </a>
    <?php endif; ?>
    <form id="registerForm" method="post" action="register.php">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit">Register</button>
       <div class="login">
       <a href="login.php" class="login-button">User Already Exist!!! </a>
       </div> 
    </form>

</div>

</body>
</html>
