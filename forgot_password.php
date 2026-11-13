<?php

include 'config/db.php';

$message = "";

if (isset($_POST['reset_request'])){
    $email = trim($_POST['email']?? '');

    if (!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $message = "Please enter a valid email address.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - Mustbedelah</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="form-container">

    <h1>Forgot Password</h1>

    <p>Enter your email address to receive a password reset link.</p>

    
    <?php if (!empty($message)): ?>
        <div class="form-message error">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>


    <form method="POST">

        <div class="form-group">
            <label>Email Address</label>
            <input
                type="email"
                name="email"
                placeholder="Enter your registered email"
                required
            >
        </div>

        <button type="submit" name="reset_request">
            Send Reset Link
        </button>

    </form>

    <p class="form-footer-text">
        <a href="login.php">Back to Login</a>
    </p>

</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>