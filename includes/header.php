
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

    $cart_sql = "SELECT COALESCE(SUM(quantity), 0) AS total_items
                 FROM cart
                 WHERE customer_id = ?";

    $stmt = mysqli_prepare($conn, $cart_sql);

    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);

    $cart_result = mysqli_stmt_get_result($stmt);
    $cart_data = mysqli_fetch_assoc($cart_result);

    $cart_count = (int) ($cart_data['total_items'] ?? 0);

    mysqli_stmt_close($stmt);
}
?>

<!-- Font Awesome Icons -->
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<nav class="navbar">

    <div class="logo">
        <a href="index.php">mustbedelah</a>
    </div>

    <ul class="nav-links">

        <li>
            <a href="index.php"
               title="Home"
               aria-label="Home">
                <i class="fa-solid fa-house"></i>
            </a>
        </li>

        <li>
            <a href="shop.php"
               title="Shop"
               aria-label="Shop">
                <i class="fa-solid fa-bag-shopping"></i>
            </a>
        </li>

        <li>
            <a href="about.php"
               title="About"
               aria-label="About">
                <i class="fa-solid fa-circle-info"></i>
            </a>
        </li>

        <li>
            <a href="about.php#final-section"
               title="Contact"
               aria-label="Contact">
                <i class="fa-solid fa-envelope"></i>
            </a>
        </li>

        <li>
            <a href="cart.php"
               title="Cart"
               aria-label="Cart"
               class="nav-cart">
                <i class="fa-solid fa-cart-shopping"></i>

                <?php if ($cart_count > 0): ?>
                    <span class="cart-badge">
                        <?php echo $cart_count; ?>
                    </span>
                <?php endif; ?>
            </a>
        </li>

        <?php if (isset($_SESSION['customer_id'])): ?>

            <li>
                <a href="my-orders.php"
                   title="My Orders"
                   aria-label="My Orders">
                    <i class="fa-solid fa-box"></i>
                </a>
            </li>

            <li>
                <a href="profile.php"
                   title="My Profile"
                   aria-label="My Profile">
                    <i class="fa-solid fa-user"></i>
                </a>
            </li>

            <li class="customer-name">
                <?php
                    echo htmlspecialchars(
                        $_SESSION['name'] ?? 'Customer',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>
            </li>

            <li>
                <a href="logout.php"
                   title="Logout"
                   aria-label="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </li>

        <?php else: ?>

            <li>
                <a href="login.php"
                   title="Sign In"
                   aria-label="Sign In">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span class="nav-signin-text">Sign In</span>
                </a>
            </li>

        <?php endif; ?>

    </ul>

</nav>
