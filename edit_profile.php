
<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$customerId = (int) $_SESSION['customer_id'];
$error = "";

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

// Generate CSRF token
if (empty($_SESSION['profile_csrf'])) {
    $_SESSION['profile_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $submittedCsrf = $_POST['csrf_token'] ?? '';

    if (!is_string($submittedCsrf) ||
        !hash_equals($_SESSION['profile_csrf'], $submittedCsrf)) {
        http_response_code(403);
        exit("Invalid form request.");
    }

    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));

    $customer = compact('name', 'email', 'phone', 'address');

    if ($name === '' || strlen($name) > 255) {
        $error = "Please enter a valid name.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)
              || strlen($email) > 255) {
        $error = "Please enter a valid email address.";

    } elseif (strlen($phone) > 20) {
        $error = "Phone number must be 20 characters or fewer.";

    } elseif (strlen($address) > 2000) {
        $error = "Delivery address is too long.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT customer_id FROM customers
             WHERE email = ? AND customer_id != ?"
        );

        mysqli_stmt_bind_param($stmt, "si", $email, $customerId);
        mysqli_stmt_execute($stmt);

        $duplicate = mysqli_fetch_assoc(
            mysqli_stmt_get_result($stmt)
        );

        mysqli_stmt_close($stmt);

        if ($duplicate) {
            $error = "This email address is already registered.";

        } else {

            try {
                $stmt = mysqli_prepare(
                    $conn,
                    "UPDATE customers
                     SET name = ?, email = ?, phone = ?, address = ?
                     WHERE customer_id = ?"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "ssssi",
                    $name,
                    $email,
                    $phone,
                    $address,
                    $customerId
                );

                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                $_SESSION['name'] = $name;
                $_SESSION['profile_csrf'] = bin2hex(random_bytes(32));

                header("Location: profile.php?updated=1");
                exit;

            } catch (Throwable $e) {
                error_log("Profile update error: " . $e->getMessage());
                $error = "Unable to update your profile. Please try again.";
            }
        }
    }
}

function fieldValue($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile - Mustbedelah</title>
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="form-container">

    <h1>Edit Profile</h1>

    <?php if ($error !== ''): ?>
        <div class="form-message error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <input type="hidden" name="csrf_token"
               value="<?php echo fieldValue($_SESSION['profile_csrf']); ?>">

        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name"
                   maxlength="255"
                   value="<?php echo fieldValue($customer['name']); ?>"
                   required>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email"
                   maxlength="255"
                   value="<?php echo fieldValue($customer['email']); ?>"
                   required>
        </div>

        <div class="form-group">
            <label>Phone Number (Optional)</label>
            <input type="tel" name="phone"
                   maxlength="20"
                   value="<?php echo fieldValue($customer['phone']); ?>"
                   placeholder="Enter phone number">
        </div>

        <div class="form-group">
            <label>Delivery Address (Optional)</label>
            <textarea name="address"
                      rows="4"
                      maxlength="2000"
                      placeholder="Enter your full delivery address"><?php
                echo fieldValue($customer['address']);
            ?></textarea>
        </div>

        <button type="submit">Save Changes</button>

    </form>

    <p class="form-footer-text">
        <a href="profile.php">Cancel / Back to Profile</a>
    </p>

</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
