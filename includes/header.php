<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cart_count = 0;

if (isset($_SESSION['customer_id'])) {

    if (!isset($conn)) {
        include __DIR__ . '/../config/db.php';
    }

    $customer_id = (int) $_SESSION['customer_id'];

    $cart_sql = "SELECT SUM(quantity) AS total_items
                 FROM cart
                 WHERE customer_id = $customer_id";

    $cart_result = mysqli_query($conn, $cart_sql);

    if ($cart_result) {
        $cart_data = mysqli_fetch_assoc($cart_result);
        $cart_count = $cart_data['total_items'] ?? 0;
    }
}
?>

<nav class="navbar">

    <div class="logo">
        <a href="index.php">mustbedelah</a>
    </div>

    <ul class="nav-links">

        <li>
            <a href="index.php">Home</a>
        </li>

        <li>
            <a href="shop.php">Shop</a>
        </li>

        <li>
            <a href="about.php">About</a>
        </li>

        <li>
            <a href="about.php#final-section">Contact</a>
        </li>

        <li>
            <a href="cart.php">
                Cart (<?php echo $cart_count; ?>)
            </a>
        </li>

        <?php if (isset($_SESSION['customer_id'])) { ?>

            <li>
                <a href="my-orders.php">My Orders</a>
            </li>

            <li class="customer-name">
                <?php echo htmlspecialchars($_SESSION['name']); ?>
            </li>

            <li>
                <a href="logout.php">Logout</a>
            </li>

        <?php } else { ?>

            <li>
                <a href="login.php">Sign In</a>
            </li>

        <?php } ?>

    </ul>

</nav>