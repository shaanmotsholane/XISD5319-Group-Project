<?php

include 'config/db.php';

$message = "";
$messageType = "";

if (isset($_POST['reset_request'])){
    $email = trim($_POST['email']?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } else {
        $sql = "SELECT customer_id FROM customers WHERE email = ?";

        $stmt = mysqli_prepare($conn,$sql);

        mysqli_stmt_bind_param($stmt,"s",$email);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $customer = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);
        
        if ($customer) {

            $token = bin2hex(random_bytes(32));

            $tokenHash = hash('sha256', $token);
     
            $customerId = $customer['customer_id'];

            $sql = "INSERT INTO password_resets
                    (customer_id, token_hash, expires_at)
                    VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param($stmt, "is", $customerId, $tokenHash);

            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);
        }
       
        $message = "If an account exists with this email address, you will receive a password reset link shortly.";
        $messageType = "success";

    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - Mustbedelah</title>
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="form-container">

    <h1>Forgot Password</h1>

    <p>Enter your email address to receive a password reset link.</p>
  
    <?php if (!empty($message)): ?>
        <div class="form-message <?php echo $messageType; ?>">
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