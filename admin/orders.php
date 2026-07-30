<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include '../config/db.php';

/*
|--------------------------------------------------------------------------
| Load all customer orders
|--------------------------------------------------------------------------
*/

$orders_sql = "
    SELECT
        orders.order_id,
        orders.order_reference,
        orders.customer_id,
        orders.total_amount,
        orders.order_date,
        customers.name AS customer_name,
        customers.email AS customer_email
    FROM orders
    LEFT JOIN customers
        ON orders.customer_id = customers.customer_id
    ORDER BY orders.order_date DESC
";

$orders_result = mysqli_query($conn, $orders_sql);

if (!$orders_result) {
    die("Unable to load customer orders.");
}

/*
|--------------------------------------------------------------------------
| Store orders in an array
|--------------------------------------------------------------------------
*/

$orders = [];
$order_ids = [];

while ($order = mysqli_fetch_assoc($orders_result)) {
    $order_id = (int) $order['order_id'];

    $order['items'] = [];

    $orders[$order_id] = $order;
    $order_ids[] = $order_id;
}

/*
|--------------------------------------------------------------------------
| Load all order items using one query
|--------------------------------------------------------------------------
*/

if (!empty($order_ids)) {
    $safe_order_ids = array_map('intval', $order_ids);
    $order_id_list = implode(',', $safe_order_ids);

    $items_sql = "
        SELECT
            order_items.order_id,
            order_items.product_id,
            order_items.size,
            order_items.quantity,
            products.product_name
        FROM order_items
        LEFT JOIN products
            ON order_items.product_id = products.product_id
        WHERE order_items.order_id IN ($order_id_list)
        ORDER BY order_items.order_id DESC
    ";

    $items_result = mysqli_query($conn, $items_sql);

    if (!$items_result) {
        die("Unable to load the order items.");
    }

    while ($item = mysqli_fetch_assoc($items_result)) {
        $order_id = (int) $item['order_id'];

        if (isset($orders[$order_id])) {
            $orders[$order_id]['items'][] = $item;
        }
    }
}

/*
|--------------------------------------------------------------------------
| Order statistics
|--------------------------------------------------------------------------
*/

$total_orders = count($orders);
$total_revenue = 0;
$total_items_ordered = 0;

foreach ($orders as $order) {
    $total_revenue += (float) $order['total_amount'];

    foreach ($order['items'] as $item) {
        $total_items_ordered += (int) $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Customer Orders | Mustbedelah</title>

    <link
        rel="icon"
        type="image/jpeg"
        href="../images/logo.jpeg"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    <link rel="stylesheet" href="admin.css">

</head>

<body>

<?php include 'admin-nav.php'; ?>

<main class="admin-orders-container">

    <div class="admin-page-heading">

        <div>
            <h1>Customer Orders</h1>

            <p>
                View customer details, ordered products and order totals.
            </p>
        </div>

    </div>

    <section class="admin-stats-grid">

        <article class="admin-stat-card">

            <div class="admin-stat-icon">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>

            <div>
                <span>Total Orders</span>

                <strong>
                    <?php echo number_format($total_orders); ?>
                </strong>
            </div>

        </article>

        <article class="admin-stat-card">

            <div class="admin-stat-icon">
                <i class="fa-solid fa-box"></i>
            </div>

            <div>
                <span>Items Ordered</span>

                <strong>
                    <?php echo number_format($total_items_ordered); ?>
                </strong>
            </div>

        </article>

        <article class="admin-stat-card">

            <div class="admin-stat-icon">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>

            <div>
                <span>Total Order Value</span>

                <strong>
                    R<?php
                    echo number_format(
                        $total_revenue,
                        2,
                        '.',
                        ','
                    );
                    ?>
                </strong>
            </div>

        </article>

    </section>

    <?php if (empty($orders)) { ?>

        <section class="admin-empty-state">

            <i class="fa-solid fa-bag-shopping"></i>

            <h2>No orders found</h2>

            <p>
                Customer orders will appear here after checkout.
            </p>

        </section>

    <?php } else { ?>

        <section class="admin-table-card">

            <div class="admin-table-wrapper">

                <table class="admin-orders-table">

                    <thead>

                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Date</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($orders as $order) { ?>

                        <tr>

                            <td data-label="Order">

                                <div class="order-reference">

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $order['order_reference']
                                        );
                                        ?>
                                    </strong>

                                    <span>
                                        Order ID:
                                        <?php
                                        echo (int) $order['order_id'];
                                        ?>
                                    </span>

                                </div>

                            </td>

                            <td data-label="Customer">

                                <div class="order-customer">

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $order['customer_name']
                                            ?? 'Customer unavailable'
                                        );
                                        ?>
                                    </strong>

                                    <a
                                        href="mailto:<?php
                                            echo htmlspecialchars(
                                                $order['customer_email']
                                                ?? ''
                                            );
                                        ?>"
                                    >
                                        <?php
                                        echo htmlspecialchars(
                                            $order['customer_email']
                                            ?? 'Email unavailable'
                                        );
                                        ?>
                                    </a>

                                </div>

                            </td>

                            <td data-label="Items">

                                <div class="admin-order-items">

                                <?php if (empty($order['items'])) { ?>

                                    <span class="order-item-unavailable">
                                        No item information available
                                    </span>

                                <?php } else { ?>

                                    <?php foreach (
                                        $order['items'] as $item
                                    ) { ?>

                                        <div class="admin-order-item">

                                            <strong>
                                                <?php
                                                echo htmlspecialchars(
                                                    $item['product_name']
                                                    ?? 'Deleted product'
                                                );
                                                ?>
                                            </strong>

                                            <span>
                                                <?php
                                                if (
                                                    !empty($item['size'])
                                                ) {
                                                    echo 'Size: '
                                                        . htmlspecialchars(
                                                            $item['size']
                                                        )
                                                        . ' · ';
                                                }
                                                ?>

                                                Quantity:
                                                <?php
                                                echo (int)
                                                    $item['quantity'];
                                                ?>
                                            </span>

                                        </div>

                                    <?php } ?>

                                <?php } ?>

                                </div>

                            </td>

                            <td data-label="Total">

                                <strong class="order-total">
                                    R<?php
                                    echo number_format(
                                        (float)
                                            $order['total_amount'],
                                        2,
                                        '.',
                                        ','
                                    );
                                    ?>
                                </strong>

                            </td>

                            <td data-label="Date">

                                <div class="order-date">

                                    <strong>
                                        <?php
                                        echo date(
                                            'd M Y',
                                            strtotime(
                                                $order['order_date']
                                            )
                                        );
                                        ?>
                                    </strong>

                                    <span>
                                        <?php
                                        echo date(
                                            'H:i',
                                            strtotime(
                                                $order['order_date']
                                            )
                                        );
                                        ?>
                                    </span>

                                </div>

                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </section>

    <?php } ?>

</main>

</body>
</html>