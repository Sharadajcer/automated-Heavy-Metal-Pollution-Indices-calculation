<?php
session_start();

// Run login check ONLY when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // DB Connection
    $conn = new mysqli("localhost", "root", "", "hmpi_system");

    // Get input
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check in database
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Successful login
        $_SESSION['username'] = $username;
        header("Location: home.php");
        exit();
    } else {
        $error_message = "Invalid Username or Password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login – HMPI</title>

<style>
    body {
        margin: 0;
        font-family: Poppins, sans-serif;
        background: url('images/water-bg2.jpg') no-repeat center center/cover;
    }
    .login-box {
        width: 350px;
        margin: 10% auto;
        background: rgba(255,255,255,0.88);
        padding: 25px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0px 0px 20px rgba(0,0,0,0.3);
    }
    input {
        width: 90%; padding: 12px;
        margin: 10px 0; border-radius: 8px;
        border: 1px solid #aaa;
    }
    button {
        width: 95%; padding: 12px;
        background: #2f4eb8; border: none;
        color: white; border-radius: 8px;
        font-size: 18px; cursor: pointer;
    }
</style>
</head>

<body>

<div class="login-box">
    <h2>Login to HMPI</h2>

    <!-- Show error message if login failed -->
    <?php if (!empty($error_message)) { ?>
        <p style="color: red; font-weight: bold;">
            <?= $error_message ?>
        </p>
    <?php } ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Enter Username" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
