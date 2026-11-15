<?php
include 'config/db.php';

$message = "";
$messageType = "";

$token = $_GET['token'] ?? '';


$validToken = false;

if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
    $message = "Invalid or missing password reset link.";
    $messageType = "error";
} else {

    $tokenHash = hash('sha256', $token);

    $sql = "SELECT customer_id
            FROM password_resets
            WHERE token_hash = ?
            AND expires_at > NOW()
            AND used_at IS NULL";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $tokenHash);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $reset = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if ($reset) {
        $validToken = true;
    } else {
        $message = "This password reset link is invalid or has expired.";
        $messageType = "error";
    }
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>Reset Password - Mustbedelah</title>
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php include 'includes/header.php'; ?>

<div class="form-container">

    <h1>Reset Password</h1>

    
    <?php if (!empty($message)): ?>
        <div class="form-message <?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>


    <p>Create a new password for your account.</p>

    <?php if ($validToken): ?>

    <form method="POST">

        <div class="form-group">
            <label>New Password</label>
            <input
                type="password"
                name="password"
                placeholder="Enter new password"
                minlength="8"
                pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,}"
                title="Use at least 8 characters, including uppercase, lowercase and a number."
                required
            >
        </div>

        <div class="form-group">
            <label>Confirm New Password</label>
            <input
                type="password"
                name="confirm_password"
                placeholder="Confirm new password"
                required
            >
        </div>

        <button type="submit" name="reset_password">
            Reset Password
        </button>

    </form>
    <?php endif; ?>

    <p class="form-footer-text">
        <a href="login.php">Back to Login</a>
    </p>

</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
