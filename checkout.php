<?php
session_start();

include 'config/db.php';

if(!isset($_SESSION['customer_id'])){
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

$check_cart = "SELECT * FROM cart
               WHERE customer_id = '$customer_id'";

$cart_result = mysqli_query($conn, $check_cart);

if(mysqli_num_rows($cart_result) == 0){

    header("Location: cart.php?error=empty");

    exit();
}

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
        WHERE cart.customer_id = '$customer_id'";

$result = mysqli_query($conn, $sql);

$total = 0;

$cart_items = [];

while($row = mysqli_fetch_assoc($result)){

    $total += $row['price'] * $row['quantity'];

    $cart_items[] = $row;
}

if(isset($_POST['place_order'])){

    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $update_customer = "UPDATE customers
                        SET phone='$phone',
                            address='$address'
                        WHERE customer_id='$customer_id'";

    mysqli_query($conn, $update_customer);

    $order_reference = "MBD-" . strtoupper(substr(md5(uniqid()), 0, 8));

    $order_sql = "INSERT INTO orders(customer_id, order_reference, total_amount)
                    VALUES('$customer_id', '$order_reference', '$total')";

    mysqli_query($conn, $order_sql);

    $order_id = mysqli_insert_id($conn);

    foreach($cart_items as $item){

    $product_id = $item['product_id'];

    $quantity = $item['quantity'];

    $size = $item['size'];

    $colour = $item['colour'];

    $price = $item['price'];

    $order_item_sql = "INSERT INTO order_items(
                        order_id,
                        product_id,
                        quantity,
                        size,
                        colour,
                        price
                    )
                    VALUES(
                        '$order_id',
                        '$product_id',
                        '$quantity',
                        '$size',
                        '$colour',
                        '$price'
                    )";

    mysqli_query($conn, $order_item_sql);

    $product_id = $item['product_id'];

    $quantity = $item['quantity'];

    $update_stock = "UPDATE products
                     SET stock_quantity = stock_quantity - $quantity
                     WHERE product_id='$product_id'";

    mysqli_query($conn, $update_stock);
    }

    $clear_cart = "DELETE FROM cart
                   WHERE customer_id='$customer_id'";

    mysqli_query($conn, $clear_cart);

    header("Location: order-success.php?ref=" . $order_reference);
exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="checkout-page">

    <div class="checkout-container">

        <section class="checkout-summary">

            <h1>Checkout</h1>

            <p class="checkout-intro">
                Review your items and enter your delivery information.
            </p>

            <div class="checkout-items">

                <?php foreach ($cart_items as $item) { ?>

                    <div class="checkout-item">

                        <img
                            src="<?php echo htmlspecialchars($item['image']); ?>"
                            alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                            class="checkout-image"
                        >

                        <div class="checkout-info">

                            <h3>
                                <?php echo htmlspecialchars($item['product_name']); ?>
                            </h3>

                            <p>
                                Size:
                                <strong>
                                    <?php echo htmlspecialchars($item['size']); ?>
                                </strong>
                            </p>

                            <p>
                                Colour:
                                <strong>
                                    <?php echo htmlspecialchars($item['colour']); ?>
                                </strong>
                            </p>

                            <p>
                                Quantity:
                                <strong>
                                    <?php echo (int) $item['quantity']; ?>
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

                <?php } ?>

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

            <form method="POST" class="checkout-form">

                <div class="form-group">

                    <label for="phone">
                        Phone Number
                    </label>

                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        placeholder="Enter your phone number"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="address">
                        Delivery Address
                    </label>

                    <textarea
                        id="address"
                        name="address"
                        placeholder="Enter your full delivery address"
                        required
                    ></textarea>

                </div>

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

                        <option value="Credit Card">
                            Credit Card
                        </option>

                        <option value="Debit Card">
                            Debit Card
                        </option>

                        <option value="EFT">
                            EFT
                        </option>

                    </select>

                </div>

                <div class="form-group">

                    <label for="card_name">
                        Name on Card
                    </label>

                    <input
                        type="text"
                        id="card_name"
                        name="card_name"
                        placeholder="Enter the cardholder's name"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="card_number">
                        Card Number
                    </label>

                    <input
                        type="text"
                        id="card_number"
                        name="card_number"
                        placeholder="Enter the card number"
                        maxlength="16"
                        inputmode="numeric"
                        required
                    >

                </div>

                <div class="card-row">

                    <div class="form-group">

                        <label for="expiry">
                            Expiry Date
                        </label>

                        <input
                            type="text"
                            id="expiry"
                            name="expiry"
                            placeholder="MM/YY"
                            maxlength="5"
                            required
                        >

                    </div>

                    <div class="form-group">

                        <label for="cvv">
                            CVV
                        </label>

                        <input
                            type="password"
                            id="cvv"
                            name="cvv"
                            placeholder="CVV"
                            maxlength="3"
                            inputmode="numeric"
                            required
                        >

                    </div>

                </div>

                <p class="payment-notice">
                    <i class="fa-solid fa-lock"></i>
                    This is a simulated payment form for the project.
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