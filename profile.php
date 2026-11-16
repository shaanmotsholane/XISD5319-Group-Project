
<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$customerId = (int) $_SESSION['customer_id'];

$stmt = mysqli_prepare(
    $conn,
    "SELECT name, email, phone, address
     FROM customers WHERE customer_id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $customerId);
mysqli_stmt_execute($stmt);

$customer = mysqli_fetch_assoc(
    mysqli_stmt_get_result($stmt)
);

mysqli_stmt_close($stmt);

if (!$customer) {
    header("Location: login.php");
    exit;
}

function displayValue($value) {
    return htmlspecialchars(
        trim((string)($value ?? '')) ?: 'Not provided',
        ENT_QUOTES,
        'UTF-8'
    );
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile - Mustbedelah</title>
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="form-container">

    <h1>My Profile</h1>

    <?php if (isset($_GET['updated'])): ?>
        <div class="form-message success">
            Your profile has been updated successfully.
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label>Full Name</label>
        <p><?php echo displayValue($customer['name']); ?></p>
    </div>

    <div class="form-group">
        <label>Email Address</label>
        <p><?php echo displayValue($customer['email']); ?></p>
    </div>

    <div class="form-group">
        <label>Phone Number</label>
        <p><?php echo displayValue($customer['phone']); ?></p>
    </div>

    <div class="form-group">
        <label>Delivery Address</label>
        <p style="white-space: pre-wrap;"><?php
            echo displayValue($customer['address']);
        ?></p>
    </div>

    <a href="edit_profile.php" class="btn">
        Edit Profile
    </a>

    <p class="form-footer-text">
        <a href="index.php">Back to Home</a>
    </p>

</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
