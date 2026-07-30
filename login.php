<?php
session_start();

include 'config/db.php';

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM customers 
            WHERE email='$email' 
            AND password='$password'";

    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){

        $row = mysqli_fetch_assoc($result);

        $_SESSION['customer_id'] = $row['customer_id'];
        $_SESSION['name'] = $row['name'];

        header("Location: index.php");

    }else{
        echo "Invalid Email or Password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <?php include 'includes/header.php'; ?>

<div class="form-container">

    <h1>Login</h1>

    <form method="POST">

        <div class="form-group">
            <label>Email Address</label>
            <input
                type="email"
                name="email"
                placeholder="Enter your email"
                required
            >
        </div>

        <div class="form-group">
            <label>Password</label>
            <input
                type="password"
                name="password"
                placeholder="Enter your password"
                required
            >
        </div>

        <button type="submit" name="login">
            Login
        </button>

        <p class="form-footer-text">
            Don't have an account?
            <a href="register.php">Register Here</a>
        </p>

        <p class="form-footer-text">
            <a href="admin/login.php">
                Login as Admin
            </a>
        </p>

    </form>

</div>

</body>
</html>