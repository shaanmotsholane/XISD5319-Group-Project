<?php
session_start();

include 'config/db.php';

$error = "";

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM customers WHERE email = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $email);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $row = mysqli_fetch_assoc($result);

    if ($row && password_verify($password, $row['password'])) {

        $_SESSION['customer_id'] = $row['customer_id'];
        $_SESSION['name'] = $row['name'];

        header("Location: index.php");
        exit();

    }else{
        $error = "Invalid Email or Password";
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

    <?php if (!empty($error)): ?>

        <div class = "form-message error">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>

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

        <p class="form-footer-text">
            <a href="forgot_password.php">Forgot Password?</a>
        </p> 

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