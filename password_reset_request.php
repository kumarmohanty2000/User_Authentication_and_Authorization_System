<?php
require 'config.php';
$success= $error =  "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows === 1) {
            $token = bin2hex(random_bytes(32));
            $expires = date("U") + 3600;
            
            $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $token, $expires);
            $stmt->execute();
            
            $reset_link = "http://saroj.com/reset_password.php?token=" . $token;
            $subject = "Password Reset Request";
            $message = "Please click on the following link to reset your password: " . $reset_link;
            mail($email, $subject, $message);
            
            $success =  "Check your email for a password reset link.";
        } else {
            $error = "No account found with that email address.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        input[type="email"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Password Reset</h1>
        <p><?php echo $success; ?></p>
        <p><?php echo $error; ?></p>
        <form method="POST" action="password_reset_request.php">
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="submit" value="Send Reset Link">
        </form>
    </div>
</body>
</html>
