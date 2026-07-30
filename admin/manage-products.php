<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include '../config/db.php';

/*
|--------------------------------------------------------------------------
| Product search
|--------------------------------------------------------------------------
*/

$search = trim($_GET['search'] ?? '');

$sql = "SELECT
            products.product_id,
            products.product_name,
            products.sku,
            products.price,
            products.stock_quantity,
            products.is_active,
            products.image,
            categories.category_name
        FROM products
        LEFT JOIN categories
            ON products.category_id = categories.category_id";

$params = [];
$types = "";

if ($search !== '') {
    $sql .= " WHERE
                products.product_name LIKE ?
                OR products.sku LIKE ?
                OR categories.category_name LIKE ?";

    $search_term = "%" . $search . "%";

    $params = [
        $search_term,
        $search_term,
        $search_term
    ];

    $types = "sss";
}

$sql .= " ORDER BY products.created_at DESC, products.product_id DESC";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Unable to load products.");
}

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

/*
|--------------------------------------------------------------------------
| Product statistics
|--------------------------------------------------------------------------
*/

$statistics_sql = "SELECT
                        COUNT(*) AS total_products,
                        SUM(
                            CASE
                                WHEN stock_quantity > 5 THEN 1
                                ELSE 0
                            END
                        ) AS in_stock,
                        SUM(
                            CASE
                                WHEN stock_quantity BETWEEN 1 AND 5 THEN 1
                                ELSE 0
                            END
                        ) AS low_stock,
                        SUM(
                            CASE
                                WHEN stock_quantity = 0 THEN 1
                                ELSE 0
                            END
                        ) AS out_of_stock
                    FROM products";

$statistics_result = mysqli_query($conn, $statistics_sql);
$statistics = mysqli_fetch_assoc($statistics_result);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Manage Products | Mustbedelah</title>

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

<main class="admin-page">

    <div class="admin-container">

        <div class="admin-page-heading admin-products-heading">

            <div>

                <p class="admin-page-label">
                    Product management
                </p>

                <h1>Manage Products</h1>

                <p>
                    Search, edit and manage products available in the store.
                </p>

            </div>

            <a href="add-product.php" class="admin-primary-button">

                <i class="fa-solid fa-plus"></i>

                Add Product

            </a>

        </div>

        <section class="admin-statistics-grid">

            <article class="admin-stat-card">

                <div class="admin-stat-icon">

                    <i class="fa-solid fa-shirt"></i>

                </div>

                <div>

                    <span>Total products</span>

                    <strong>
                        <?php echo (int) $statistics['total_products']; ?>
                    </strong>

                </div>

            </article>

            <article class="admin-stat-card">

                <div class="admin-stat-icon">

                    <i class="fa-solid fa-circle-check"></i>

                </div>

                <div>

                    <span>In stock</span>

                    <strong>
                        <?php echo (int) $statistics['in_stock']; ?>
                    </strong>

                </div>

            </article>

            <article class="admin-stat-card">

                <div class="admin-stat-icon">

                    <i class="fa-solid fa-triangle-exclamation"></i>

                </div>

                <div>

                    <span>Low stock</span>

                    <strong>
                        <?php echo (int) $statistics['low_stock']; ?>
                    </strong>

                </div>

            </article>

            <article class="admin-stat-card">

                <div class="admin-stat-icon">

                    <i class="fa-solid fa-circle-xmark"></i>

                </div>

                <div>

                    <span>Out of stock</span>

                    <strong>
                        <?php echo (int) $statistics['out_of_stock']; ?>
                    </strong>

                </div>

            </article>

        </section>

        <section class="admin-products-panel">

            <div class="admin-products-toolbar">

                <form
                    method="GET"
                    action="manage-products.php"
                    class="admin-search-form"
                >

                    <div class="admin-search-input-wrapper">

                        <i class="fa-solid fa-magnifying-glass"></i>

                        <input
                            type="search"
                            name="search"
                            placeholder="Search by product, SKU or category"
                            value="<?php echo htmlspecialchars($search); ?>"
                        >

                    </div>

                    <button type="submit" class="admin-search-button">
                        Search
                    </button>

                    <?php if ($search !== '') { ?>

                        <a
                            href="manage-products.php"
                            class="admin-clear-search"
                        >
                            Clear
                        </a>

                    <?php } ?>

                </form>

            </div>

            <?php if (mysqli_num_rows($result) === 0) { ?>

                <div class="admin-empty-state">

                    <i class="fa-solid fa-box-open"></i>

                    <h2>No products found</h2>

                    <p>
                        No products match your current search.
                    </p>

                    <?php if ($search !== '') { ?>

                        <a
                            href="manage-products.php"
                            class="admin-secondary-button"
                        >
                            Clear Search
                        </a>

                    <?php } else { ?>

                        <a
                            href="add-product.php"
                            class="admin-primary-button"
                        >
                            Add First Product
                        </a>

                    <?php } ?>

                </div>

            <?php } else { ?>

                <div class="admin-products-table-wrapper">

                    <table class="admin-products-table">

                        <thead>

                            <tr>

                                <th>Product</th>

                                <th>SKU</th>

                                <th>Category</th>

                                <th>Price</th>

                                <th>Stock</th>

                                <th>Status</th>

                                <th>Actions</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php while ($product = mysqli_fetch_assoc($result)) { ?>

                                <tr>

                                    <td data-label="Product">

                                        <div class="admin-product-info">

                                            <img
                                                src="../<?php echo htmlspecialchars($product['image']); ?>"
                                                alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                                class="admin-product-image"
                                            >

                                            <div>

                                                <strong>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $product['product_name']
                                                    );
                                                    ?>
                                                </strong>

                                                <span>
                                                    Product ID:
                                                    <?php echo (int) $product['product_id']; ?>
                                                </span>

                                            </div>

                                        </div>

                                    </td>

                                    <td data-label="SKU">

                                        <?php
                                        echo htmlspecialchars(
                                            $product['sku']
                                        );
                                        ?>

                                    </td>

                                    <td data-label="Category">

                                        <?php
                                        echo htmlspecialchars(
                                            $product['category_name']
                                            ?? 'Uncategorised'
                                        );
                                        ?>

                                    </td>

                                    <td data-label="Price">

                                        R<?php
                                        echo number_format(
                                            (float) $product['price'],
                                            2
                                        );
                                        ?>

                                    </td>

                                    <td data-label="Stock">

                                        <?php
                                        $stock_quantity =
                                            (int) $product['stock_quantity'];
                                        ?>

                                        <?php if ($stock_quantity > 5) { ?>

                                            <span class="admin-stock-badge admin-stock-available">

                                                <?php echo $stock_quantity; ?>
                                                available

                                            </span>

                                        <?php } elseif ($stock_quantity > 0) { ?>

                                            <span class="admin-stock-badge admin-stock-low">

                                                Low stock:
                                                <?php echo $stock_quantity; ?>

                                            </span>

                                        <?php } else { ?>

                                            <span class="admin-stock-badge admin-stock-empty">
                                                Out of stock
                                            </span>

                                        <?php } ?>

                                    </td>

                                    <td data-label="Status">

                                        <?php if ((int) $product['is_active'] === 1) { ?>

                                            <span class="admin-status-badge admin-status-active">
                                                Active
                                            </span>

                                        <?php } else { ?>

                                            <span class="admin-status-badge admin-status-hidden">
                                                Hidden
                                            </span>

                                        <?php } ?>

                                    </td>

                                    <td data-label="Actions">

                                        <div class="admin-product-actions">

                                            <a
                                                href="edit-product.php?id=<?php echo (int) $product['product_id']; ?>"
                                                class="admin-action-button admin-edit-button"
                                            >

                                                <i class="fa-solid fa-pen"></i>

                                                Edit

                                            </a>

                                            <a
                                                href="delete-product.php?id=<?php echo (int) $product['product_id']; ?>"
                                                class="admin-action-button admin-delete-button"
                                                onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.');"
                                            >

                                                <i class="fa-solid fa-trash"></i>

                                                Delete

                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            <?php } ?>

        </section>

    </div>

</main>

</body>

</html>