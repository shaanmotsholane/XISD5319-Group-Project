<?php
session_start();

include 'config/db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

$sql = "SELECT
            cart.cart_id,
            cart.quantity,
            cart.size,
            cart.colour,
            products.product_id,
            products.product_name,
            products.price,
            products.image
        FROM cart
        INNER JOIN products
        ON cart.product_id = products.product_id
        WHERE cart.customer_id = '$customer_id'";

$result = mysqli_query($conn, $sql);

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="cart-page">

    <div class="cart-container">

        <div class="cart-heading">

            <div>
                <h1>Your Cart</h1>

                <p>
                    Review your selected items before proceeding to checkout.
                </p>
            </div>

            <a href="shop.php" class="cart-shopping-link">
                <i class="fa-solid fa-arrow-left"></i>
                Continue Shopping
            </a>

        </div>

        <?php if (
            isset($_GET['error']) &&
            $_GET['error'] === 'empty'
        ) { ?>

            <div class="cart-message cart-error-message">

                <i class="fa-solid fa-circle-exclamation"></i>

                Your cart is empty. Add a product before proceeding to checkout.

            </div>

        <?php } ?>

        <div class="cart-layout">

            <section class="cart-items-section">

                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                    <?php
                    $item_total = $row['price'] * $row['quantity'];
                    $total += $item_total;
                    ?>

                    <article class="cart-item">

                        <a
                            href="product.php?id=<?php echo (int) $row['product_id']; ?>"
                            class="cart-image-link"
                        >

                            <img
                                src="<?php echo htmlspecialchars($row['image']); ?>"
                                alt="<?php echo htmlspecialchars($row['product_name']); ?>"
                                class="cart-image"
                            >

                        </a>

                        <div class="cart-info">

                            <a
                                href="product.php?id=<?php echo (int) $row['product_id']; ?>"
                                class="cart-product-name"
                            >
                                <?php echo htmlspecialchars($row['product_name']); ?>
                            </a>

                            <div class="cart-product-options">

                                <span>
                                    Size:
                                    <strong>
                                        <?php echo htmlspecialchars($row['size']); ?>
                                    </strong>
                                </span>

                                <span>
                                    Colour:
                                    <strong>
                                        <?php echo htmlspecialchars($row['colour']); ?>
                                    </strong>
                                </span>

                            </div>

                            <p class="cart-unit-price">
                                R<?php echo number_format((float) $row['price'], 2); ?>
                                each
                            </p>

                            <div class="quantity-controls">

                                <a
                                    href="update-cart.php?cart_id=<?php echo (int) $row['cart_id']; ?>&action=decrease"
                                    class="quantity-button"
                                    aria-label="Decrease quantity"
                                >
                                    <i class="fa-solid fa-minus"></i>
                                </a>

                                <span class="quantity-value">
                                    <?php echo (int) $row['quantity']; ?>
                                </span>

                                <a
                                    href="update-cart.php?cart_id=<?php echo (int) $row['cart_id']; ?>&action=increase"
                                    class="quantity-button"
                                    aria-label="Increase quantity"
                                >
                                    <i class="fa-solid fa-plus"></i>
                                </a>

                            </div>

                        </div>

                        <div class="cart-item-right">

                            <strong class="cart-item-price">
                                R<?php echo number_format($item_total, 2); ?>
                            </strong>

                            <a
                                href="remove-from-cart.php?id=<?php echo (int) $row['cart_id']; ?>"
                                class="cart-remove-link"
                                aria-label="Remove <?php echo htmlspecialchars($row['product_name']); ?> from cart"
                            >
                                <i class="fa-regular fa-trash-can"></i>
                                Remove
                            </a>

                        </div>

                    </article>

                <?php } ?>

                <?php if ($total <= 0) { ?>

                    <div class="empty-cart">

                        <i class="fa-solid fa-cart-shopping"></i>

                        <h2>Your cart is empty</h2>

                        <p>
                            Browse the shop and add products to your cart.
                        </p>

                        <a href="shop.php" class="btn">
                            Start Shopping
                        </a>

                    </div>

                <?php } ?>

            </section>

            <?php if ($total > 0) { ?>

                <aside class="cart-summary">

                    <h2>Order Summary</h2>

                    <div class="cart-summary-row">

                        <span>Subtotal</span>

                        <span>
                            R<?php echo number_format($total, 2); ?>
                        </span>

                    </div>

                    <div class="cart-summary-row">

                        <span>Delivery</span>

                        <span>Calculated at checkout</span>

                    </div>

                    <div class="cart-summary-total">

                        <span>Total</span>

                        <strong>
                            R<?php echo number_format($total, 2); ?>
                        </strong>

                    </div>

                    <a href="checkout.php" class="btn cart-checkout-button">

                        Proceed to Checkout

                        <i class="fa-solid fa-arrow-right"></i>

                    </a>

                    <p class="cart-summary-note">

                        <i class="fa-solid fa-lock"></i>

                        Secure checkout

                    </p>

                </aside>

            <?php } ?>

        </div>

    </div>

</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>