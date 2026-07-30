<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include '../config/db.php';

/*
|--------------------------------------------------------------------------
| General report totals
|--------------------------------------------------------------------------
*/

$summary_sql = "
    SELECT
        (SELECT COUNT(*) FROM products) AS total_products,
        (SELECT COUNT(*) FROM customers) AS total_customers,
        (SELECT COUNT(*) FROM orders) AS total_orders,
        (SELECT COUNT(*) FROM contact_messages) AS total_messages,
        (
            SELECT COALESCE(SUM(total_amount), 0)
            FROM orders
        ) AS total_revenue,
        (
            SELECT COALESCE(AVG(total_amount), 0)
            FROM orders
        ) AS average_order_value,
        (
            SELECT COALESCE(SUM(stock_quantity), 0)
            FROM products
        ) AS total_stock
";

$summary_result = mysqli_query($conn, $summary_sql);

if (!$summary_result) {
    die("Unable to load report information.");
}

$summary = mysqli_fetch_assoc($summary_result);

/*
|--------------------------------------------------------------------------
| Product stock information
|--------------------------------------------------------------------------
*/

$stock_sql = "
    SELECT
        SUM(
            CASE
                WHEN stock_quantity > 5 THEN 1
                ELSE 0
            END
        ) AS in_stock_products,

        SUM(
            CASE
                WHEN stock_quantity BETWEEN 1 AND 5 THEN 1
                ELSE 0
            END
        ) AS low_stock_products,

        SUM(
            CASE
                WHEN stock_quantity = 0 THEN 1
                ELSE 0
            END
        ) AS out_of_stock_products
    FROM products
";

$stock_result = mysqli_query($conn, $stock_sql);

if (!$stock_result) {
    die("Unable to load stock information.");
}

$stock_summary = mysqli_fetch_assoc($stock_result);

/*
|--------------------------------------------------------------------------
| Recent monthly sales
|--------------------------------------------------------------------------
*/

$monthly_sales_sql = "
    SELECT
        DATE_FORMAT(order_date, '%Y-%m') AS month_value,
        DATE_FORMAT(order_date, '%b %Y') AS month_label,
        COUNT(*) AS total_orders,
        COALESCE(SUM(total_amount), 0) AS total_revenue
    FROM orders
    WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
    GROUP BY
        DATE_FORMAT(order_date, '%Y-%m'),
        DATE_FORMAT(order_date, '%b %Y')
    ORDER BY month_value DESC
";

$monthly_sales_result = mysqli_query(
    $conn,
    $monthly_sales_sql
);

if (!$monthly_sales_result) {
    die("Unable to load monthly sales.");
}

$monthly_sales = [];

while (
    $monthly_row = mysqli_fetch_assoc(
        $monthly_sales_result
    )
) {
    $monthly_sales[] = $monthly_row;
}

/*
|--------------------------------------------------------------------------
| Top-selling products
|--------------------------------------------------------------------------
*/

$top_products_sql = "
    SELECT
        products.product_id,
        products.product_name,
        products.image,
        COALESCE(SUM(order_items.quantity), 0) AS units_sold
    FROM products
    LEFT JOIN order_items
        ON products.product_id = order_items.product_id
    GROUP BY
        products.product_id,
        products.product_name,
        products.image
    ORDER BY units_sold DESC, products.product_name ASC
    LIMIT 5
";

$top_products_result = mysqli_query(
    $conn,
    $top_products_sql
);

if (!$top_products_result) {
    die("Unable to load product sales information.");
}

$top_products = [];

while (
    $product_row = mysqli_fetch_assoc(
        $top_products_result
    )
) {
    $top_products[] = $product_row;
}

/*
|--------------------------------------------------------------------------
| Report values
|--------------------------------------------------------------------------
*/

$total_products =
    (int) ($summary['total_products'] ?? 0);

$total_customers =
    (int) ($summary['total_customers'] ?? 0);

$total_orders =
    (int) ($summary['total_orders'] ?? 0);

$total_messages =
    (int) ($summary['total_messages'] ?? 0);

$total_revenue =
    (float) ($summary['total_revenue'] ?? 0);

$average_order_value =
    (float) ($summary['average_order_value'] ?? 0);

$total_stock =
    (int) ($summary['total_stock'] ?? 0);

$in_stock_products =
    (int) ($stock_summary['in_stock_products'] ?? 0);

$low_stock_products =
    (int) ($stock_summary['low_stock_products'] ?? 0);

$out_of_stock_products =
    (int) ($stock_summary['out_of_stock_products'] ?? 0);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Reports | Mustbedelah</title>

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

<main class="admin-reports-container">

    <div class="admin-page-heading">

        <div>

            <h1>Reports</h1>

            <p>
                Review store performance, customer activity and product stock.
            </p>

        </div>

        <button
            type="button"
            class="admin-print-button"
            onclick="window.print()"
        >
            <i class="fa-solid fa-print"></i>
            Print Report
        </button>

    </div>

    <section class="reports-summary-grid">

        <article class="report-summary-card">

            <div class="report-summary-icon">
                <i class="fa-solid fa-shirt"></i>
            </div>

            <div>
                <span>Total Products</span>

                <strong>
                    <?php echo number_format($total_products); ?>
                </strong>
            </div>

        </article>

        <article class="report-summary-card">

            <div class="report-summary-icon">
                <i class="fa-solid fa-users"></i>
            </div>

            <div>
                <span>Total Customers</span>

                <strong>
                    <?php echo number_format($total_customers); ?>
                </strong>
            </div>

        </article>

        <article class="report-summary-card">

            <div class="report-summary-icon">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>

            <div>
                <span>Total Orders</span>

                <strong>
                    <?php echo number_format($total_orders); ?>
                </strong>
            </div>

        </article>

        <article class="report-summary-card">

            <div class="report-summary-icon">
                <i class="fa-solid fa-envelope"></i>
            </div>

            <div>
                <span>Total Messages</span>

                <strong>
                    <?php echo number_format($total_messages); ?>
                </strong>
            </div>

        </article>

        <article class="report-summary-card report-revenue-card">

            <div class="report-summary-icon">
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

        <article class="report-summary-card">

            <div class="report-summary-icon">
                <i class="fa-solid fa-receipt"></i>
            </div>

            <div>
                <span>Average Order</span>

                <strong>
                    R<?php
                    echo number_format(
                        $average_order_value,
                        2,
                        '.',
                        ','
                    );
                    ?>
                </strong>
            </div>

        </article>

    </section>

    <section class="reports-content-grid">

        <article class="report-panel">

            <div class="report-panel-heading">

                <div>
                    <h2>Stock Overview</h2>

                    <p>
                        Current product and inventory levels.
                    </p>
                </div>

                <i class="fa-solid fa-boxes-stacked"></i>

            </div>

            <div class="stock-report-list">

                <div class="stock-report-row">

                    <span>Total Stock Units</span>

                    <strong>
                        <?php echo number_format($total_stock); ?>
                    </strong>

                </div>

                <div class="stock-report-row">

                    <span>Products In Stock</span>

                    <strong>
                        <?php
                        echo number_format(
                            $in_stock_products
                        );
                        ?>
                    </strong>

                </div>

                <div class="stock-report-row">

                    <span>Low Stock Products</span>

                    <strong class="report-warning-value">
                        <?php
                        echo number_format(
                            $low_stock_products
                        );
                        ?>
                    </strong>

                </div>

                <div class="stock-report-row">

                    <span>Out of Stock Products</span>

                    <strong class="report-danger-value">
                        <?php
                        echo number_format(
                            $out_of_stock_products
                        );
                        ?>
                    </strong>

                </div>

            </div>

        </article>

        <article class="report-panel">

            <div class="report-panel-heading">

                <div>
                    <h2>Order Summary</h2>

                    <p>
                        Overall order and customer activity.
                    </p>
                </div>

                <i class="fa-solid fa-chart-line"></i>

            </div>

            <div class="stock-report-list">

                <div class="stock-report-row">

                    <span>Orders Per Customer</span>

                    <strong>
                        <?php
                        echo $total_customers > 0
                            ? number_format(
                                $total_orders /
                                $total_customers,
                                2
                            )
                            : '0.00';
                        ?>
                    </strong>

                </div>

                <div class="stock-report-row">

                    <span>Average Order Value</span>

                    <strong>
                        R<?php
                        echo number_format(
                            $average_order_value,
                            2,
                            '.',
                            ','
                        );
                        ?>
                    </strong>

                </div>

                <div class="stock-report-row">

                    <span>Total Customer Messages</span>

                    <strong>
                        <?php
                        echo number_format(
                            $total_messages
                        );
                        ?>
                    </strong>

                </div>

                <div class="stock-report-row">

                    <span>Total Revenue Recorded</span>

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

            </div>

        </article>

    </section>

    <section class="reports-content-grid">

        <article class="report-panel report-wide-panel">

            <div class="report-panel-heading">

                <div>
                    <h2>Recent Monthly Sales</h2>

                    <p>
                        Order totals recorded during recent months.
                    </p>
                </div>

                <i class="fa-solid fa-calendar-days"></i>

            </div>

            <?php if (empty($monthly_sales)) { ?>

                <div class="report-empty-state">

                    <i class="fa-solid fa-chart-column"></i>

                    <p>
                        No monthly sales information is available yet.
                    </p>

                </div>

            <?php } else { ?>

                <div class="report-table-wrapper">

                    <table class="report-table">

                        <thead>

                            <tr>
                                <th>Month</th>
                                <th>Orders</th>
                                <th>Order Value</th>
                            </tr>

                        </thead>

                        <tbody>

                        <?php foreach (
                            $monthly_sales as $month
                        ) { ?>

                            <tr>

                                <td data-label="Month">

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $month['month_label']
                                        );
                                        ?>
                                    </strong>

                                </td>

                                <td data-label="Orders">

                                    <?php
                                    echo number_format(
                                        (int)
                                            $month['total_orders']
                                    );
                                    ?>

                                </td>

                                <td data-label="Order Value">

                                    <strong>
                                        R<?php
                                        echo number_format(
                                            (float)
                                                $month['total_revenue'],
                                            2,
                                            '.',
                                            ','
                                        );
                                        ?>
                                    </strong>

                                </td>

                            </tr>

                        <?php } ?>

                        </tbody>

                    </table>

                </div>

            <?php } ?>

        </article>

        <article class="report-panel">

            <div class="report-panel-heading">

                <div>
                    <h2>Top Products</h2>

                    <p>
                        Products ranked by units ordered.
                    </p>
                </div>

                <i class="fa-solid fa-ranking-star"></i>

            </div>

            <?php if (empty($top_products)) { ?>

                <div class="report-empty-state">

                    <p>
                        No product sales information is available.
                    </p>

                </div>

            <?php } else { ?>

                <div class="top-products-list">

                    <?php foreach (
                        $top_products as $index => $product
                    ) { ?>

                        <div class="top-product-row">

                            <span class="top-product-position">
                                <?php echo $index + 1; ?>
                            </span>

                            <?php if (
                                !empty($product['image'])
                            ) { ?>

                                <img
                                    src="../<?php
                                    echo htmlspecialchars(
                                        $product['image']
                                    );
                                    ?>"
                                    alt="<?php
                                    echo htmlspecialchars(
                                        $product['product_name']
                                    );
                                    ?>"
                                >

                            <?php } ?>

                            <div class="top-product-details">

                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $product['product_name']
                                    );
                                    ?>
                                </strong>

                                <span>
                                    <?php
                                    echo number_format(
                                        (int)
                                            $product['units_sold']
                                    );
                                    ?>
                                    units ordered
                                </span>

                            </div>

                        </div>

                    <?php } ?>

                </div>

            <?php } ?>

        </article>

    </section>

</main>

</body>
</html>