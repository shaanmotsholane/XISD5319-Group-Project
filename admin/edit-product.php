<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include '../config/db.php';

$message = "";
$message_type = "";

/*
|--------------------------------------------------------------------------
| Validate product ID
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
| Load categories
|--------------------------------------------------------------------------
*/

$category_sql = "
    SELECT category_id, category_name
    FROM categories
    WHERE is_active = 1
    ORDER BY category_name ASC
";

$category_result = mysqli_query($conn, $category_sql);

if (!$category_result) {
    die("Unable to load categories.");
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
        category_id,
        sku,
        price,
        description,
        stock_quantity,
        image,
        is_active
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

/*
|--------------------------------------------------------------------------
| Set form values
|--------------------------------------------------------------------------
*/

$product_name = $product['product_name'];
$category_id = $product['category_id'];
$sku = $product['sku'];
$price = $product['price'];
$description = $product['description'];
$stock_quantity = $product['stock_quantity'];
$image = $product['image'];
$is_active = (int) $product['is_active'];

/*
|--------------------------------------------------------------------------
| Update product
|--------------------------------------------------------------------------
*/

if (isset($_POST['update_product'])) {
    $product_name = trim(
        $_POST['product_name'] ?? ''
    );

    $category_id = (int) (
        $_POST['category_id'] ?? 0
    );

    $sku = strtoupper(
        trim($_POST['sku'] ?? '')
    );

    $price = $_POST['price'] ?? '';

    $description = trim(
        $_POST['description'] ?? ''
    );

    $stock_quantity =
        $_POST['stock_quantity'] ?? '';

    $image = trim(
        $_POST['image'] ?? ''
    );

    $is_active =
        isset($_POST['is_active']) ? 1 : 0;

    /*
    |--------------------------------------------------------------------------
    | Validate form values
    |--------------------------------------------------------------------------
    */

    if ($product_name === '') {
        $message = "Please enter the product name.";
        $message_type = "error";

    } elseif ($category_id <= 0) {
        $message = "Please select a category.";
        $message_type = "error";

    } elseif ($sku === '') {
        $message = "Please enter a product SKU.";
        $message_type = "error";

    } elseif (!preg_match('/^[A-Z0-9\-]+$/', $sku)) {
        $message =
            "The SKU may only contain letters, numbers and hyphens.";

        $message_type = "error";

    } elseif (
        $price === '' ||
        !is_numeric($price) ||
        (float) $price < 0
    ) {
        $message = "Please enter a valid product price.";
        $message_type = "error";

    } elseif (
        $stock_quantity === '' ||
        filter_var(
            $stock_quantity,
            FILTER_VALIDATE_INT
        ) === false ||
        (int) $stock_quantity < 0
    ) {
        $message =
            "Stock quantity must be zero or greater.";

        $message_type = "error";

    } elseif ($image === '') {
        $message =
            "Please enter the product image path.";

        $message_type = "error";

    } else {
        /*
        |--------------------------------------------------------------------------
        | Confirm that the category exists
        |--------------------------------------------------------------------------
        */

        $category_check = mysqli_prepare(
            $conn,
            "SELECT category_id
             FROM categories
             WHERE category_id = ?
               AND is_active = 1
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $category_check,
            "i",
            $category_id
        );

        mysqli_stmt_execute($category_check);

        $category_check_result =
            mysqli_stmt_get_result(
                $category_check
            );

        if (
            mysqli_num_rows(
                $category_check_result
            ) === 0
        ) {
            $message =
                "The selected category does not exist.";

            $message_type = "error";
        }

        mysqli_stmt_close($category_check);
    }

    /*
    |--------------------------------------------------------------------------
    | Check whether another product uses the SKU
    |--------------------------------------------------------------------------
    */

    if ($message === '') {
        $sku_check = mysqli_prepare(
            $conn,
            "SELECT product_id
             FROM products
             WHERE sku = ?
               AND product_id != ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $sku_check,
            "si",
            $sku,
            $product_id
        );

        mysqli_stmt_execute($sku_check);

        $sku_result =
            mysqli_stmt_get_result($sku_check);

        if (mysqli_num_rows($sku_result) > 0) {
            $message =
                "That SKU is already being used by another product.";

            $message_type = "error";
        }

        mysqli_stmt_close($sku_check);
    }

    /*
    |--------------------------------------------------------------------------
    | Save changes
    |--------------------------------------------------------------------------
    */

    if ($message === '') {
        $price_value = (float) $price;
        $stock_value = (int) $stock_quantity;

        mysqli_begin_transaction($conn);

        try {
            $update_statement = mysqli_prepare(
                $conn,
                "UPDATE products
                 SET
                    product_name = ?,
                    category_id = ?,
                    sku = ?,
                    price = ?,
                    description = ?,
                    stock_quantity = ?,
                    image = ?,
                    is_active = ?
                 WHERE product_id = ?"
            );

            if (!$update_statement) {
                throw new Exception(
                    "Unable to prepare the product update."
                );
            }

            mysqli_stmt_bind_param(
                $update_statement,
                "sisdsisii",
                $product_name,
                $category_id,
                $sku,
                $price_value,
                $description,
                $stock_value,
                $image,
                $is_active,
                $product_id
            );

            if (
                !mysqli_stmt_execute(
                    $update_statement
                )
            ) {
                throw new Exception(
                    "The product could not be updated."
                );
            }

            mysqli_stmt_close($update_statement);

            /*
            |--------------------------------------------------------------------------
            | Update the primary image record
            |--------------------------------------------------------------------------
            */

            $primary_image_check =
                mysqli_prepare(
                    $conn,
                    "SELECT image_id
                     FROM product_images
                     WHERE product_id = ?
                       AND is_primary = 1
                     LIMIT 1"
                );

            mysqli_stmt_bind_param(
                $primary_image_check,
                "i",
                $product_id
            );

            mysqli_stmt_execute(
                $primary_image_check
            );

            $primary_image_result =
                mysqli_stmt_get_result(
                    $primary_image_check
                );

            $primary_image =
                mysqli_fetch_assoc(
                    $primary_image_result
                );

            mysqli_stmt_close(
                $primary_image_check
            );

            if ($primary_image) {
                $image_id =
                    (int) $primary_image['image_id'];

                $update_image =
                    mysqli_prepare(
                        $conn,
                        "UPDATE product_images
                         SET
                            image_path = ?,
                            alt_text = ?
                         WHERE image_id = ?"
                    );

                mysqli_stmt_bind_param(
                    $update_image,
                    "ssi",
                    $image,
                    $product_name,
                    $image_id
                );

                if (
                    !mysqli_stmt_execute(
                        $update_image
                    )
                ) {
                    throw new Exception(
                        "The primary image could not be updated."
                    );
                }

                mysqli_stmt_close($update_image);

            } else {
                $is_primary = 1;
                $display_order = 1;

                $insert_image =
                    mysqli_prepare(
                        $conn,
                        "INSERT INTO product_images (
                            product_id,
                            image_path,
                            alt_text,
                            is_primary,
                            display_order
                         )
                         VALUES (?, ?, ?, ?, ?)"
                    );

                mysqli_stmt_bind_param(
                    $insert_image,
                    "issii",
                    $product_id,
                    $image,
                    $product_name,
                    $is_primary,
                    $display_order
                );

                if (
                    !mysqli_stmt_execute(
                        $insert_image
                    )
                ) {
                    throw new Exception(
                        "The primary image could not be created."
                    );
                }

                mysqli_stmt_close($insert_image);
            }

            mysqli_commit($conn);

            /*
            |--------------------------------------------------------------------------
            | Update Firebase
            |--------------------------------------------------------------------------
            */

            $firebase_url =
                "https://mustbedelah-5504b-default-rtdb.firebaseio.com/products/"
                . $product_id
                . ".json";

            $firebase_data = [
                "product_id" => $product_id,
                "name" => $product_name,
                "category_id" => $category_id,
                "sku" => $sku,
                "price" => $price_value,
                "description" => $description,
                "stock_quantity" => $stock_value,
                "image" => $image,
                "is_active" => $is_active
            ];

            $curl = curl_init();

            curl_setopt(
                $curl,
                CURLOPT_URL,
                $firebase_url
            );

            curl_setopt(
                $curl,
                CURLOPT_CUSTOMREQUEST,
                "PUT"
            );

            curl_setopt(
                $curl,
                CURLOPT_POSTFIELDS,
                json_encode($firebase_data)
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

            curl_setopt(
                $curl,
                CURLOPT_HTTPHEADER,
                [
                    "Content-Type: application/json"
                ]
            );

            $firebase_response =
                curl_exec($curl);

            $firebase_error =
                curl_error($curl);

            curl_close($curl);

            $message =
                "Product updated successfully.";

            $message_type = "success";

            if ($firebase_error !== '') {
                $message .=
                    " The Firebase copy could not be updated.";
            }

        } catch (Throwable $exception) {
            mysqli_rollback($conn);

            $message =
                "The product could not be updated. Please try again.";

            $message_type = "error";
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

    <title>Edit Product | Mustbedelah</title>

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

<main class="edit-product-container">

    <div class="edit-product-heading">

        <div>
            <h1>Edit Product</h1>

            <p>
                Update the product information, stock and visibility.
            </p>
        </div>

        <a
            href="manage-products.php"
            class="back-button"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Back to Products
        </a>

    </div>

    <?php if ($message !== '') { ?>

        <div class="message message-<?php echo $message_type; ?>">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php } ?>

    <form method="POST" class="product-form">

        <div class="form-grid">

            <div class="form-group form-group-full">

                <label for="product_name">
                    Product name
                </label>

                <input
                    type="text"
                    id="product_name"
                    name="product_name"
                    value="<?php
                        echo htmlspecialchars(
                            $product_name
                        );
                    ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label for="sku">
                    SKU
                </label>

                <input
                    type="text"
                    id="sku"
                    name="sku"
                    value="<?php
                        echo htmlspecialchars($sku);
                    ?>"
                    required
                >

                <small>
                    Use letters, numbers and hyphens.
                </small>

            </div>

            <div class="form-group">

                <label for="category_id">
                    Category
                </label>

                <select
                    id="category_id"
                    name="category_id"
                    required
                >

                    <option value="">
                        Select a category
                    </option>

                    <?php
                    mysqli_data_seek(
                        $category_result,
                        0
                    );
                    ?>

                    <?php while (
                        $category =
                            mysqli_fetch_assoc(
                                $category_result
                            )
                    ) { ?>

                        <option
                            value="<?php
                                echo (int)
                                    $category['category_id'];
                            ?>"
                            <?php
                            echo (
                                (int) $category_id
                                ===
                                (int) $category['category_id']
                            )
                                ? 'selected'
                                : '';
                            ?>
                        >
                            <?php
                            echo htmlspecialchars(
                                $category['category_name']
                            );
                            ?>
                        </option>

                    <?php } ?>

                </select>

            </div>

            <div class="form-group">

                <label for="price">
                    Price
                </label>

                <input
                    type="number"
                    id="price"
                    name="price"
                    min="0"
                    step="0.01"
                    value="<?php
                        echo htmlspecialchars(
                            (string) $price
                        );
                    ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label for="stock_quantity">
                    Stock quantity
                </label>

                <input
                    type="number"
                    id="stock_quantity"
                    name="stock_quantity"
                    min="0"
                    step="1"
                    value="<?php
                        echo htmlspecialchars(
                            (string) $stock_quantity
                        );
                    ?>"
                    required
                >

            </div>

            <div class="form-group form-group-full">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="6"
                ><?php
                    echo htmlspecialchars(
                        $description
                    );
                ?></textarea>

            </div>

            <div class="form-group form-group-full">

                <label for="image">
                    Image path
                </label>

                <input
                    type="text"
                    id="image"
                    name="image"
                    value="<?php
                        echo htmlspecialchars($image);
                    ?>"
                    placeholder="images/shirt.jpg"
                    required
                >

                <small>
                    Example: images/shirt.jpg
                </small>

                <div class="current-image">

                    <img
                        src="../<?php
                            echo htmlspecialchars($image);
                        ?>"
                        alt="<?php
                            echo htmlspecialchars(
                                $product_name
                            );
                        ?>"
                    >

                    <div>
                        <strong>Current image</strong>

                        <span>
                            <?php
                            echo htmlspecialchars($image);
                            ?>
                        </span>
                    </div>

                </div>

            </div>

            <div class="form-group form-group-full">

                <label class="active-checkbox">

                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        <?php
                        echo $is_active === 1
                            ? 'checked'
                            : '';
                        ?>
                    >

                    <span>
                        <strong>Active product</strong><br>

                        <small>
                            Uncheck this to hide the product from the shop.
                        </small>
                    </span>

                </label>

            </div>

        </div>

        <div class="form-actions">

            <a
                href="manage-products.php"
                class="cancel-button"
            >
                Cancel
            </a>

            <button
                type="submit"
                name="update_product"
                class="form-button"
            >
                <i class="fa-solid fa-floppy-disk"></i>
                Update Product
            </button>

        </div>

    </form>

</main>

</body>
</html>