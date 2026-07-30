<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$customer_id = (int) $_SESSION['customer_id'];
$order_reference = trim($_GET['ref'] ?? '');
$order = null;

/*
|--------------------------------------------------------------------------
| Confirm that the order belongs to the customer
|--------------------------------------------------------------------------
*/

if ($order_reference !== '') {

    $sql = "SELECT
                order_reference,
                total_amount,
                order_date
            FROM orders
            WHERE order_reference = ?
              AND customer_id = ?
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {

        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $order_reference,
            $customer_id
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $order = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);
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

    <title>Order Successful | Mustbedelah</title>

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

<main class="order-success-page">

    <section class="success-container">

        <?php if ($order) { ?>

            <div class="success-icon">

                <i class="fa-solid fa-check"></i>

            </div>

            <p class="success-label">
                Order confirmed
            </p>

            <h1>Thank You for Your Order</h1>

            <p class="success-introduction">
                Your order has been received and is now being processed.
            </p>

            <div class="success-order-details">

                <div class="success-detail">

                    <span>Order reference</span>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $order['order_reference']
                        );
                        ?>
                    </strong>

                </div>

                <div class="success-detail">

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

                <div class="success-detail">

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

            <div class="success-message">

                <i class="fa-solid fa-box"></i>

                <div>

                    <h2>What happens next?</h2>

                    <p>
                        We will process your order and provide delivery updates
                        when they become available.
                    </p>

                </div>

            </div>

            <div class="success-actions">

                <a href="my-orders.php" class="btn">

                    <i class="fa-solid fa-receipt"></i>

                    View My Orders

                </a>

                <a href="shop.php" class="success-secondary-button">

                    <i class="fa-solid fa-bag-shopping"></i>

                    Continue Shopping

                </a>

            </div>

        <?php } else { ?>

            <div class="success-error-icon">

                <i class="fa-solid fa-circle-exclamation"></i>

            </div>

            <h1>Order Details Not Found</h1>

            <p class="success-introduction">
                We could not find an order matching this reference.
            </p>

            <div class="success-actions">

                <a href="my-orders.php" class="btn">
                    View My Orders
                </a>

                <a href="shop.php" class="success-secondary-button">
                    Return to Shop
                </a>

            </div>

        <?php } ?>

    </section>

</main>

<?php include 'includes/footer.php'; ?>

</body>

</html>