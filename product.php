<?php
session_start();

include 'config/db.php';

$error_message = "";

/*
|--------------------------------------------------------------------------
| Validate the product ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: shop.php");
    exit();
}

$product_id = (int) $_GET['id'];

/*
|--------------------------------------------------------------------------
| Retrieve the product safely
|--------------------------------------------------------------------------
*/

$sql = "SELECT
            product_id,
            product_name,
            price,
            image,
            description,
            stock_quantity
        FROM products
        WHERE product_id = ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Unable to load the product.");
}

mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

/*
|--------------------------------------------------------------------------
| Product not found
|--------------------------------------------------------------------------
*/

if (!$product) {
    http_response_code(404);
}

/*
|--------------------------------------------------------------------------
| Error messages
|--------------------------------------------------------------------------
*/

if (isset($_GET['error']) && $_GET['error'] === "stock") {
    $error_message = "The requested quantity exceeds the available stock.";
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

    <title>
        <?php
        echo $product
            ? htmlspecialchars($product['product_name']) . " | Mustbedelah"
            : "Product Not Found | Mustbedelah";
        ?>
    </title>

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

<main class="single-product-page">

    <div class="single-product-container">

        <?php if (!$product) { ?>

            <section class="product-not-found">

                <i class="fa-solid fa-box-open"></i>

                <h1>Product Not Found</h1>

                <p>
                    The product may have been removed or is no longer available.
                </p>

                <a href="shop.php" class="btn">
                    Return to Shop
                </a>

            </section>

        <?php } else { ?>

            <div class="single-product-layout">

                <section class="single-product-image-section">

                    <div class="single-product-image-wrapper">

                        <img
                            src="<?php echo htmlspecialchars($product['image']); ?>"
                            alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                            class="single-product-image"
                        >

                    </div>

                </section>

                <section class="single-product-details">

                    <a href="shop.php" class="product-back-link">

                        <i class="fa-solid fa-arrow-left"></i>

                        Back to Shop

                    </a>

                    <h1>
                        <?php echo htmlspecialchars($product['product_name']); ?>
                    </h1>

                    <p class="single-product-price">

                        R<?php
                        echo number_format(
                            (float) $product['price'],
                            2
                        );
                        ?>

                    </p>

                    <?php if ((int) $product['stock_quantity'] > 5) { ?>

                        <p class="product-stock product-in-stock">

                            <i class="fa-solid fa-circle-check"></i>

                            In stock

                        </p>

                    <?php } elseif ((int) $product['stock_quantity'] > 0) { ?>

                        <p class="product-stock product-low-stock">

                            <i class="fa-solid fa-triangle-exclamation"></i>

                            Only
                            <?php echo (int) $product['stock_quantity']; ?>
                            remaining

                        </p>

                    <?php } else { ?>

                        <p class="product-stock product-out-of-stock">

                            <i class="fa-solid fa-circle-xmark"></i>

                            Out of stock

                        </p>

                    <?php } ?>

                    <div class="single-product-description">

                        <h2>Product Description</h2>

                        <p>
                            <?php
                            echo nl2br(
                                htmlspecialchars($product['description'])
                            );
                            ?>
                        </p>

                    </div>

                    <?php if ($error_message !== "") { ?>

                        <div class="product-error-message">

                            <i class="fa-solid fa-circle-exclamation"></i>

                            <?php echo htmlspecialchars($error_message); ?>

                        </div>

                    <?php } ?>

                    <form
                        method="POST"
                        action="add-to-cart.php"
                        class="product-purchase-form"
                    >

                        <input
                            type="hidden"
                            name="product_id"
                            value="<?php echo (int) $product['product_id']; ?>"
                        >

                        <div class="product-form-row">

                            <div class="form-group">

                                <label for="size">
                                    Size
                                </label>

                                <select
                                    id="size"
                                    name="size"
                                    required
                                >

                                    <option value="">
                                        Choose a size
                                    </option>

                                    <option value="S">Small</option>

                                    <option value="M">Medium</option>

                                    <option value="L">Large</option>

                                    <option value="XL">Extra Large</option>

                                </select>

                            </div>

                            <div class="form-group">

                                <label for="colour">
                                    Colour
                                </label>

                                <select
                                    id="colour"
                                    name="colour"
                                    required
                                >

                                    <option value="">
                                        Choose a colour
                                    </option>

                                    <option value="Black">Black</option>

                                    <option value="White">White</option>

                                    <option value="Grey">Grey</option>

                                    <option value="Blue">Blue</option>

                                </select>

                            </div>

                        </div>

                        <div class="form-group product-quantity-group">

                            <label for="quantity">
                                Quantity
                            </label>

                            <input
                                type="number"
                                id="quantity"
                                name="quantity"
                                value="1"
                                min="1"
                                max="<?php echo (int) $product['stock_quantity']; ?>"
                                required
                            >

                        </div>

                        <?php if ((int) $product['stock_quantity'] > 0) { ?>

                            <button
                                type="submit"
                                class="product-add-button"
                            >

                                <i class="fa-solid fa-cart-plus"></i>

                                Add to Cart

                            </button>

                        <?php } else { ?>

                            <button
                                type="button"
                                class="product-add-button"
                                disabled
                            >

                                Out of Stock

                            </button>

                        <?php } ?>

                    </form>

                    <div class="product-service-details">

                        <div>

                            <i class="fa-solid fa-truck"></i>

                            <span>
                                Delivery details confirmed during checkout
                            </span>

                        </div>

                        <div>

                            <i class="fa-solid fa-shield-halved"></i>

                            <span>
                                Secure checkout
                            </span>

                        </div>

                    </div>

                </section>

            </div>

        <?php } ?>

    </div>

</main>

<?php include 'includes/footer.php'; ?>

</body>

</html>