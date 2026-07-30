<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include '../config/db.php';

/*
|--------------------------------------------------------------------------
| Create a CSRF token
|--------------------------------------------------------------------------
*/

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/*
|--------------------------------------------------------------------------
| Validate the product ID
|--------------------------------------------------------------------------
*/

$product_id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$product_id || $product_id <= 0) {
    header("Location: manage-products.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Load the product
|--------------------------------------------------------------------------
*/

$product_statement = mysqli_prepare(
    $conn,
    "SELECT
        product_id,
        product_name,
        sku,
        image
     FROM products
     WHERE product_id = ?
     LIMIT 1"
);

if (!$product_statement) {
    die("Unable to prepare the product query.");
}

mysqli_stmt_bind_param(
    $product_statement,
    "i",
    $product_id
);

mysqli_stmt_execute($product_statement);

$product_result = mysqli_stmt_get_result(
    $product_statement
);

$product = mysqli_fetch_assoc($product_result);

mysqli_stmt_close($product_statement);

if (!$product) {
    header("Location: manage-products.php");
    exit();
}

$message = "";

/*
|--------------------------------------------------------------------------
| Delete the product after confirmation
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $submitted_token = $_POST['csrf_token'] ?? '';

    if (
        !hash_equals(
            $_SESSION['csrf_token'],
            $submitted_token
        )
    ) {
        $message = "The request could not be verified. Please try again.";

    } elseif (!isset($_POST['confirm_delete'])) {
        $message = "Please confirm that you want to delete this product.";

    } else {

        mysqli_begin_transaction($conn);

        try {

            /*
            |--------------------------------------------------------------------------
            | Delete product gallery images from the database
            |--------------------------------------------------------------------------
            */

            $delete_images = mysqli_prepare(
                $conn,
                "DELETE FROM product_images
                 WHERE product_id = ?"
            );

            if (!$delete_images) {
                throw new Exception(
                    "Unable to prepare the image deletion."
                );
            }

            mysqli_stmt_bind_param(
                $delete_images,
                "i",
                $product_id
            );

            if (!mysqli_stmt_execute($delete_images)) {
                throw new Exception(
                    "Unable to delete the product images."
                );
            }

            mysqli_stmt_close($delete_images);

            /*
            |--------------------------------------------------------------------------
            | Delete the product
            |--------------------------------------------------------------------------
            */

            $delete_product = mysqli_prepare(
                $conn,
                "DELETE FROM products
                 WHERE product_id = ?"
            );

            if (!$delete_product) {
                throw new Exception(
                    "Unable to prepare the product deletion."
                );
            }

            mysqli_stmt_bind_param(
                $delete_product,
                "i",
                $product_id
            );

            if (!mysqli_stmt_execute($delete_product)) {
                throw new Exception(
                    mysqli_stmt_error($delete_product)
                );
            }

            mysqli_stmt_close($delete_product);

            mysqli_commit($conn);

            /*
            |--------------------------------------------------------------------------
            | Delete the Firebase product record
            |--------------------------------------------------------------------------
            */

            $firebase_url =
                "https://mustbedelah-5504b-default-rtdb.firebaseio.com/products/"
                . $product_id
                . ".json";

            $curl = curl_init();

            curl_setopt(
                $curl,
                CURLOPT_URL,
                $firebase_url
            );

            curl_setopt(
                $curl,
                CURLOPT_CUSTOMREQUEST,
                "DELETE"
            );

            curl_setopt(
                $curl,
                CURLOPT_RETURNTRANSFER,
                true
            );

            curl_setopt(
                $curl,
                CURLOPT_TIMEOUT,
                10
            );

            curl_exec($curl);
            curl_close($curl);

            $_SESSION['product_message'] =
                "Product deleted successfully.";

            header("Location: manage-products.php");
            exit();

        } catch (Throwable $exception) {

            mysqli_rollback($conn);

            $message =
                "This product could not be deleted. It may already be connected to an existing order.";
        }
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

    <title>Delete Product | Mustbedelah</title>

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

<main class="delete-product-container">

    <div class="delete-product-card">

        <div class="delete-warning-icon">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>

        <h1>Delete Product</h1>

        <p>
            Are you sure you want to permanently delete this product?
        </p>

        <?php if ($message !== '') { ?>

            <div class="message message-error">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php } ?>

        <div class="delete-product-details">

            <?php if (!empty($product['image'])) { ?>

                <img
                    src="../<?php
                        echo htmlspecialchars($product['image']);
                    ?>"
                    alt="<?php
                        echo htmlspecialchars($product['product_name']);
                    ?>"
                >

            <?php } ?>

            <div>

                <strong>
                    <?php
                    echo htmlspecialchars(
                        $product['product_name']
                    );
                    ?>
                </strong>

                <span>
                    SKU:
                    <?php
                    echo htmlspecialchars(
                        $product['sku'] ?? 'Not available'
                    );
                    ?>
                </span>

            </div>

        </div>

        <p class="delete-warning-text">
            This action cannot be undone.
        </p>

        <form method="POST" class="delete-product-form">

            <input
                type="hidden"
                name="csrf_token"
                value="<?php
                    echo htmlspecialchars(
                        $_SESSION['csrf_token']
                    );
                ?>"
            >

            <div class="delete-product-actions">

                <a
                    href="manage-products.php"
                    class="cancel-button"
                >
                    <i class="fa-solid fa-arrow-left"></i>
                    Cancel
                </a>

                <button
                    type="submit"
                    name="confirm_delete"
                    class="delete-confirm-button"
                >
                    <i class="fa-solid fa-trash"></i>
                    Delete Product
                </button>

            </div>

        </form>

    </div>

</main>

</body>
</html>