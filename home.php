<?php
session_start();

require 'config.php';

$user_id = $_SESSION['user_id'];

$query = "SELECT username FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $username = $row['username'];

    echo "<h2>Welcome, $username!</h2>";
} else {
    echo "Error: Unable to fetch user name.";
}

mysqli_close($conn);
?>

<?php

$pageTitle = "Dashboard";
ob_start();

?>

<h2>Welcome <?php echo $username; ?>! to User Authentication and Authorization System</h2>
<div class="message">
    <p>You can create, update, delete your data seemlessly</p>
    <p>It is a secure connection cloud system</p>
    <p>Thank You</p>
    <p>From Saroj</p>

</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>