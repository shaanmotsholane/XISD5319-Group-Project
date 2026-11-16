
<?php

include 'config/db.php';

$message = "";
$messageType = "";
$testResetLink = "";

// Only allow the temporary test link on localhost
$isLocal = in_array(
    $_SERVER['REMOTE_ADDR'] ?? '',
    ['127.0.0.1', '::1'],
    true
);

if (isset($_POST['reset_request'])) {

    $emailInput = $_POST['email'] ?? '';
    $email = is_string($emailInput) ? trim($emailInput) : '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $messageType = "error";

    } else {

        // Use the same message for registered and unregistered emails
        $message = "If an account exists with this email address, a password reset request has been created.";
        $messageType = "success";

        try {

            // Find the customer
            $sql = "SELECT customer_id
                    FROM customers
                    WHERE email = ?";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param($stmt, "s", $email);

            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            $customer = mysqli_fetch_assoc($result);

            mysqli_stmt_close($stmt);

            if ($customer) {

                // Generate a secure token
                $token = bin2hex(random_bytes(32));

                // Store only the hash in the database
                $tokenHash = hash('sha256', $token);

                $customerId = (int) $customer['customer_id'];

                // Save the token hash with a 30-minute expiry
                $sql = "INSERT INTO password_resets
                        (customer_id, token_hash, expires_at)
                        VALUES (
                            ?,
                            ?,
                            DATE_ADD(NOW(), INTERVAL 30 MINUTE)
                        )";

                $stmt = mysqli_prepare($conn, $sql);

                mysqli_stmt_bind_param(
                    $stmt,
                    "is",
                    $customerId,
                    $tokenHash
                );

                mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);

                // TEMPORARY: Show the real reset link on localhost
                if ($isLocal) {

                    $testResetLink =
                        "http://localhost/XISD5319-Group-Project/reset_password.php?token="
                        . urlencode($token);
                }

                // Email delivery will be added later.
            }

        } catch (Throwable $e) {

            error_log("Password reset request failed: " . $e->getMessage());

            $testResetLink = "";

            $message = "Unable to process your request right now. Please try again later.";
            $messageType = "error";
        }
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

    <p>Enter your email address to request a password reset link.</p>

    <?php if (!empty($message)): ?>

        <div class="form-message <?php echo htmlspecialchars($messageType); ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <!-- Temporary local testing link -->
    <?php if ($isLocal && !empty($testResetLink)): ?>

        <div class="form-message success">

            <p><strong>Local Testing Only</strong></p>

            <p>Your password reset link has been generated.</p>

            <a href="<?php echo htmlspecialchars($testResetLink, ENT_QUOTES, 'UTF-8'); ?>">
                Open Password Reset Link
            </a>

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
