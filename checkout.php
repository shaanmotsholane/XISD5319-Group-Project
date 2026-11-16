
<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$customerId = (int) $_SESSION['customer_id'];
$error = "";

function checkoutEscape($value) {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// Get saved customer details
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

// Automatically use saved profile information
$phone = $customer['phone'] ?? '';
$address = $customer['address'] ?? '';
$paymentMethod = "";

// Get cart items
$sql = "SELECT
            cart.cart_id,
            cart.product_id,
            cart.quantity,
            cart.size,
            cart.colour,
            products.product_name,
            products.price,
            products.image,
            products.stock_quantity
        FROM cart
        INNER JOIN products
            ON cart.product_id = products.product_id
        WHERE cart.customer_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $customerId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$cartItems = [];
$total = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $cartItems[] = $row;
    $total += (float)$row['price'] * (int)$row['quantity'];
}

mysqli_stmt_close($stmt);

if (empty($cartItems)) {
    header("Location: cart.php?error=empty");
    exit;
}

// CSRF protection
if (empty($_SESSION['checkout_csrf'])) {
    $_SESSION['checkout_csrf'] = bin2hex(random_bytes(32));
}

// Process checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['place_order'])) {

    $phone = trim((string)($_POST['phone'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));
    $paymentMethod = (string)($_POST['payment_method'] ?? '');
    $csrf = $_POST['csrf_token'] ?? '';

    $allowedPayments = [
        'Credit Card',
        'Debit Card',
        'EFT'
    ];

    if (!is_string($csrf) ||
        !hash_equals($_SESSION['checkout_csrf'], $csrf)) {

        $error = "Invalid checkout request. Please refresh the page.";

    } elseif ($phone === '' || strlen($phone) > 20) {

        $error = "Please enter a valid phone number.";

    } elseif ($address === '' || strlen($address) > 2000) {

        $error = "Please enter your delivery address.";

    } elseif (!in_array($paymentMethod, $allowedPayments, true)) {

        $error = "Please select a payment method.";

    } else {

        try {
            mysqli_begin_transaction($conn);

            // Lock cart rows and products during checkout
            $sql = "SELECT
                        cart.product_id,
                        cart.quantity,
                        cart.size,
                        cart.colour,
                        products.price,
                        products.stock_quantity
                    FROM cart
                    INNER JOIN products
                        ON cart.product_id = products.product_id
                    WHERE cart.customer_id = ?
                    ORDER BY cart.product_id
                    FOR UPDATE";

            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $customerId);
            mysqli_stmt_execute($stmt);

            $lockedResult = mysqli_stmt_get_result($stmt);
            $lockedItems = [];
            $orderTotal = 0;
            $quantities = [];

            while ($item = mysqli_fetch_assoc($lockedResult)) {
                $lockedItems[] = $item;

                $productId = (int)$item['product_id'];
                $quantity = (int)$item['quantity'];

                if ($quantity <= 0) {
                    throw new Exception("Invalid cart quantity.");
                }

                $quantities[$productId] =
                    ($quantities[$productId] ?? 0) + $quantity;

                $orderTotal +=
                    (float)$item['price'] * $quantity;
            }

            mysqli_stmt_close($stmt);

            if (empty($lockedItems)) {
                throw new Exception("Your cart is empty.");
            }

            foreach ($lockedItems as $item) {
                $productId = (int)$item['product_id'];

                if ($quantities[$productId] >
                    (int)$item['stock_quantity']) {
                    throw new Exception(
                        "One or more products have insufficient stock."
                    );
                }
            }

            // Generate unique order reference
            $orderReference = "MBD-" .
                strtoupper(bin2hex(random_bytes(8)));

            // Create order
            $sql = "INSERT INTO orders
                    (customer_id, order_reference, total_amount)
                    VALUES (?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "isd",
                $customerId,
                $orderReference,
                $orderTotal
            );

            mysqli_stmt_execute($stmt);
            $orderId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            // Save delivery details for this order
            $sql = "INSERT INTO order_delivery_details
                    (order_id, phone, address, payment_method)
                    VALUES (?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "isss",
                $orderId,
                $phone,
                $address,
                $paymentMethod
            );

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Save ordered products
            $sql = "INSERT INTO order_items
                    (order_id, product_id, quantity,
                     size, colour, price)
                    VALUES (?, ?, ?, ?, ?, ?)";

            $itemStmt = mysqli_prepare($conn, $sql);

            foreach ($lockedItems as $item) {

                $productId = (int)$item['product_id'];
                $quantity = (int)$item['quantity'];
                $size = (string)$item['size'];
                $colour = (string)$item['colour'];
                $price = (float)$item['price'];

                mysqli_stmt_bind_param(
                    $itemStmt,
                    "iiissd",
                    $orderId,
                    $productId,
                    $quantity,
                    $size,
                    $colour,
                    $price
                );

                mysqli_stmt_execute($itemStmt);
            }

            mysqli_stmt_close($itemStmt);

            // Update stock
            $stockStmt = mysqli_prepare(
                $conn,
                "UPDATE products
                 SET stock_quantity = stock_quantity - ?
                 WHERE product_id = ?
                   AND stock_quantity >= ?"
            );

            foreach ($quantities as $productId => $quantity) {

                mysqli_stmt_bind_param(
                    $stockStmt,
                    "iii",
                    $quantity,
                    $productId,
                    $quantity
                );

                mysqli_stmt_execute($stockStmt);

                if (mysqli_stmt_affected_rows($stockStmt) !== 1) {
                    throw new Exception(
                        "Insufficient stock for a product."
                    );
                }
            }

            mysqli_stmt_close($stockStmt);

            // Clear customer cart
            $stmt = mysqli_prepare(
                $conn,
                "DELETE FROM cart WHERE customer_id = ?"
            );

            mysqli_stmt_bind_param($stmt, "i", $customerId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            mysqli_commit($conn);

            $_SESSION['checkout_csrf'] =
                bin2hex(random_bytes(32));

            header(
                "Location: order-success.php?ref="
                . urlencode($orderReference)
            );
            exit;

        } catch (Throwable $e) {

            mysqli_rollback($conn);

            error_log("Checkout error: " . $e->getMessage());

            $error = "Unable to place your order. "
                   . "Please check your cart and try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout - Mustbedelah</title>
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php include 'includes/header.php'; ?>

<main class="checkout-page">

<div class="checkout-container">

    <section class="checkout-summary">

        <h1>Checkout</h1>

        <p class="checkout-intro">
            Review your items and delivery information.
        </p>

        <?php if ($error !== ''): ?>
            <div class="form-message error">
                <?php echo checkoutEscape($error); ?>
            </div>
        <?php endif; ?>

        <div class="checkout-items">

            <?php foreach ($cartItems as $item): ?>

                <div class="checkout-item">

                    <img
                        src="<?php echo checkoutEscape($item['image']); ?>"
                        alt="<?php echo checkoutEscape($item['product_name']); ?>"
                        class="checkout-image"
                    >

                    <div class="checkout-info">

                        <h3>
                            <?php echo checkoutEscape($item['product_name']); ?>
                        </h3>

                        <p>
                            Size:
                            <strong>
                                <?php echo checkoutEscape($item['size']); ?>
                            </strong>
                        </p>

                        <p>
                            Colour:
                            <strong>
                                <?php echo checkoutEscape($item['colour']); ?>
                            </strong>
                        </p>

                        <p>
                            Quantity:
                            <strong>
                                <?php echo (int)$item['quantity']; ?>
                            </strong>
                        </p>

                    </div>

                    <div class="checkout-item-price">
                        R<?php
                            echo number_format(
                                $item['price'] * $item['quantity'],
                                2
                            );
                        ?>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>

        <div class="checkout-total">
            <span>Order Total</span>
            <strong>
                R<?php echo number_format($total, 2); ?>
            </strong>
        </div>

    </section>

    <section class="checkout-details">

        <h2>Delivery and Payment</h2>

        <p>
            Your saved profile details will be filled in
            automatically if available.
        </p>

        <form method="POST" class="checkout-form">

            <input
                type="hidden"
                name="csrf_token"
                value="<?php echo checkoutEscape($_SESSION['checkout_csrf']); ?>"
            >

            <div class="form-group">
                <label for="phone">Phone Number</label>

                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    maxlength="20"
                    value="<?php echo checkoutEscape($phone); ?>"
                    placeholder="Enter your phone number"
                    required
                >
            </div>

            <div class="form-group">
                <label for="address">Delivery Address</label>

                <textarea
                    id="address"
                    name="address"
                    maxlength="2000"
                    placeholder="Enter your full delivery address"
                    required
                ><?php echo checkoutEscape($address); ?></textarea>
            </div>

            <p>
                <a href="edit_profile.php">
                    Edit Saved Profile Details
                </a>
            </p>

            <div class="form-group">
                <label for="payment_method">
                    Payment Method
                </label>

                <select
                    id="payment_method"
                    name="payment_method"
                    required
                >
                    <option value="">
                        Select payment method
                    </option>

                    <?php foreach (['Credit Card', 'Debit Card', 'EFT'] as $method): ?>
                        <option
                            value="<?php echo checkoutEscape($method); ?>"
                            <?php echo $paymentMethod === $method ? 'selected' : ''; ?>
                        >
                            <?php echo checkoutEscape($method); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <p class="payment-notice">
                This is a simulated payment for the college project.
                No actual payment will be processed.
            </p>

            <button
                type="submit"
                name="place_order"
                class="checkout-btn"
            >
                Place Order
            </button>

        </form>

    </section>

</div>

</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
