<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$customer_id = (int) $_SESSION['customer_id'];

/*
|--------------------------------------------------------------------------
| Retrieve orders and their items
|--------------------------------------------------------------------------
*/

$sql = "SELECT
            orders.order_id,
            orders.order_reference,
            orders.total_amount,
            orders.order_date,
            order_items.quantity,
            order_items.size,
            order_items.colour,
            order_items.price,
            products.product_id,
            products.product_name,
            products.image
        FROM orders
        LEFT JOIN order_items
            ON orders.order_id = order_items.order_id
        LEFT JOIN products
            ON order_items.product_id = products.product_id
        WHERE orders.customer_id = ?
        ORDER BY orders.order_date DESC,
                 orders.order_id DESC,
                 order_items.order_item_id ASC";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Unable to load your orders.");
}

mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

/*
|--------------------------------------------------------------------------
| Group items under each order
|--------------------------------------------------------------------------
*/

$orders = [];

while ($row = mysqli_fetch_assoc($result)) {

    $order_id = (int) $row['order_id'];

    if (!isset($orders[$order_id])) {

        $orders[$order_id] = [
            'order_id' => $order_id,
            'order_reference' => $row['order_reference'],
            'total_amount' => $row['total_amount'],
            'order_date' => $row['order_date'],
            'items' => []
        ];
    }

    if ($row['product_name'] !== null) {

        $orders[$order_id]['items'][] = [
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'image' => $row['image'],
            'quantity' => $row['quantity'],
            'size' => $row['size'],
            'colour' => $row['colour'],
            'price' => $row['price']
        ];
    }
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Orders | Mustbedelah</title>

    <link
        rel="icon"
        type="image/jpeg"
        href="images/logo.jpeg"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    <link rel="stylesheet" href="style.css">

</head>

<body>

<?php include 'includes/header.php'; ?>

<main class="orders-page">

    <div class="orders-container">

        <div class="orders-heading">

            <div>

                <h1>My Orders</h1>

                <p>
                    View your previous purchases and order details.
                </p>

            </div>

            <a href="shop.php" class="orders-shop-link">

                <i class="fa-solid fa-bag-shopping"></i>

                Continue Shopping

            </a>

        </div>

        <?php if (empty($orders)) { ?>

            <section class="orders-empty">

                <i class="fa-solid fa-box-open"></i>

                <h2>You have no orders yet</h2>

                <p>
                    Products you purchase will appear here.
                </p>

                <a href="shop.php" class="btn">
                    Browse Products
                </a>

            </section>

        <?php } else { ?>

            <div class="orders-list">

                <?php foreach ($orders as $order) { ?>

                    <article class="order-card">

                        <div class="order-card-header">

                            <div class="order-reference">

                                <span>Order reference</span>

                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $order['order_reference']
                                    );
                                    ?>
                                </strong>

                            </div>

                            <div class="order-date">

                                <span>Order date</span>

                                <strong>
                                    <?php
                                    echo date(
                                        "d M Y",
                                        strtotime($order['order_date'])
                                    );
                                    ?>
                                </strong>

                            </div>

                            <div class="order-total">

                                <span>Order total</span>

                                <strong>
                                    R<?php
                                    echo number_format(
                                        (float) $order['total_amount'],
                                        2
                                    );
                                    ?>
                                </strong>

                            </div>

                        </div>

                        <div class="order-card-body">

                            <h2>Items</h2>

                            <?php if (empty($order['items'])) { ?>

                                <p class="order-no-items">
                                    No item information is available for this order.
                                </p>

                            <?php } else { ?>

                                <div class="order-items">

                                    <?php foreach ($order['items'] as $item) { ?>

                                        <div class="order-item">

                                            <a
                                                href="product.php?id=<?php echo (int) $item['product_id']; ?>"
                                                class="order-item-image-link"
                                            >

                                                <img
                                                    src="<?php echo htmlspecialchars($item['image']); ?>"
                                                    alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                    class="order-item-image"
                                                >

                                            </a>

                                            <div class="order-item-details">

                                                <a
                                                    href="product.php?id=<?php echo (int) $item['product_id']; ?>"
                                                    class="order-item-name"
                                                >
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $item['product_name']
                                                    );
                                                    ?>
                                                </a>

                                                <div class="order-item-options">

                                                    <span>
                                                        Size:
                                                        <strong>
                                                            <?php
                                                            echo htmlspecialchars(
                                                                $item['size']
                                                            );
                                                            ?>
                                                        </strong>
                                                    </span>

                                                    <span>
                                                        Colour:
                                                        <strong>
                                                            <?php
                                                            echo htmlspecialchars(
                                                                $item['colour']
                                                            );
                                                            ?>
                                                        </strong>
                                                    </span>

                                                    <span>
                                                        Quantity:
                                                        <strong>
                                                            <?php
                                                            echo (int) $item['quantity'];
                                                            ?>
                                                        </strong>
                                                    </span>

                                                </div>

                                            </div>

                                            <div class="order-item-price">

                                                R<?php
                                                echo number_format(
                                                    (float) $item['price'] *
                                                    (int) $item['quantity'],
                                                    2
                                                );
                                                ?>

                                            </div>

                                        </div>

                                    <?php } ?>

                                </div>

                            <?php } ?>

                        </div>

                    </article>

                <?php } ?>

            </div>

        <?php } ?>

    </div>

</main>

<?php include 'includes/footer.php'; ?>

</body>

</html>