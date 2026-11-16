
<?php
include 'config/db.php';

$message = "";
$messageType = "";
$validToken = false;
$resetSuccess = false;

$token = $_GET['token'] ?? '';

if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
    $message = "Invalid or missing password reset link.";
    $messageType = "error";

} else {

    $tokenHash = hash('sha256', $token);

    // Verify token
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

// Process password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {

    if (!$validToken) {
        $message = "Invalid or expired password reset link.";
        $messageType = "error";

    } else {

        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (
            !is_string($password) ||
            strlen($password) < 8 ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password)
        ) {
            $message = "Password must have at least 8 characters, uppercase, lowercase and a number.";
            $messageType = "error";

        } elseif (!is_string($confirmPassword) || $password !== $confirmPassword) {
            $message = "Passwords do not match.";
            $messageType = "error";

        } else {

            try {
                mysqli_begin_transaction($conn);

                // Lock the token record to prevent simultaneous reuse
                $sql = "SELECT customer_id
                        FROM password_resets
                        WHERE token_hash = ?
                          AND expires_at > NOW()
                          AND used_at IS NULL
                        FOR UPDATE";

                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "s", $tokenHash);
                mysqli_stmt_execute($stmt);

                $result = mysqli_stmt_get_result($stmt);
                $lockedReset = mysqli_fetch_assoc($result);
                mysqli_stmt_close($stmt);

                if (!$lockedReset) {
                    throw new Exception("Reset link is no longer valid.");
                }

                $customerId = (int) $lockedReset['customer_id'];
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Update customer's password
                $sql = "UPDATE customers
                        SET password = ?
                        WHERE customer_id = ?";

                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "si", $hashedPassword, $customerId);
                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_affected_rows($stmt) !== 1) {
                    throw new Exception("Customer password could not be updated.");
                }

                mysqli_stmt_close($stmt);

                // Invalidate all outstanding reset tokens for this customer
                $sql = "UPDATE password_resets
                        SET used_at = NOW()
                        WHERE customer_id = ?
                          AND used_at IS NULL";

                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $customerId);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                mysqli_commit($conn);

                $resetSuccess = true;
                $validToken = false;

                header("Location: login.php?reset=success");
                exit;

            } catch (Throwable $e) {

                mysqli_rollback($conn);

                error_log("Password reset failed: " . $e->getMessage());

                $message = "Unable to reset password. Please request a new reset link.";
                $messageType = "error";
                $validToken = false;
            }
        }
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
        <div class="form-message <?php echo htmlspecialchars($messageType); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <p>Create a new password for your account.</p>

    <?php if ($validToken): ?>

        <form method="POST"
              action="<?php echo htmlspecialchars('reset_password.php?token=' . urlencode($token), ENT_QUOTES, 'UTF-8'); ?>">

            <div class="form-group">
                <label>New Password</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Enter new password"
                    minlength="8"
                    pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,}"
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
