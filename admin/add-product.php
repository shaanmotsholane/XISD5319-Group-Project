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
| Add product
|--------------------------------------------------------------------------
*/

if (isset($_POST['add_product'])) {

    $product_name = trim($_POST['product_name'] ?? '');
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $sku = strtoupper(trim($_POST['sku'] ?? ''));
    $price = (float) ($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $stock_quantity = (int) ($_POST['stock_quantity'] ?? 0);
    $image = trim($_POST['image'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    /*
    |--------------------------------------------------------------------------
    | Basic validation
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
        $message = "The SKU may only contain letters, numbers and hyphens.";
        $message_type = "error";

    } elseif ($price < 0) {
        $message = "The product price cannot be negative.";
        $message_type = "error";

    } elseif ($stock_quantity < 0) {
        $message = "The stock quantity cannot be negative.";
        $message_type = "error";

    } elseif ($image === '') {
        $message = "Please enter the product image path.";
        $message_type = "error";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Check whether the SKU already exists
        |--------------------------------------------------------------------------
        */

        $sku_check = mysqli_prepare(
            $conn,
            "SELECT product_id
             FROM products
             WHERE sku = ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $sku_check,
            "s",
            $sku
        );

        mysqli_stmt_execute($sku_check);

        $sku_result = mysqli_stmt_get_result($sku_check);

        if (mysqli_num_rows($sku_result) > 0) {

            $message = "That SKU is already being used by another product.";
            $message_type = "error";

        } else {

            /*
            |--------------------------------------------------------------------------
            | Insert the product
            |--------------------------------------------------------------------------
            */

            $insert_product = mysqli_prepare(
                $conn,
                "INSERT INTO products (
                    product_name,
                    category_id,
                    sku,
                    price,
                    description,
                    stock_quantity,
                    image,
                    is_active
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            if (!$insert_product) {

                $message = "Unable to prepare the product insert.";
                $message_type = "error";

            } else {

                mysqli_stmt_bind_param(
                    $insert_product,
                    "sisdsisi",
                    $product_name,
                    $category_id,
                    $sku,
                    $price,
                    $description,
                    $stock_quantity,
                    $image,
                    $is_active
                );

                if (mysqli_stmt_execute($insert_product)) {

                    $product_id = mysqli_insert_id($conn);

                    /*
                    |--------------------------------------------------------------------------
                    | Add the current image to product_images
                    |--------------------------------------------------------------------------
                    */

                    $is_primary = 1;
                    $display_order = 1;

                    $insert_image = mysqli_prepare(
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

                    if ($insert_image) {

                        mysqli_stmt_bind_param(
                            $insert_image,
                            "issii",
                            $product_id,
                            $image,
                            $product_name,
                            $is_primary,
                            $display_order
                        );

                        mysqli_stmt_execute($insert_image);
                        mysqli_stmt_close($insert_image);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Firebase integration
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
                        "price" => $price,
                        "description" => $description,
                        "stock_quantity" => $stock_quantity,
                        "image" => $image,
                        "is_active" => $is_active
                    ];

                    $data_json = json_encode($firebase_data);

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, $firebase_url);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

                    curl_setopt(
                        $ch,
                        CURLOPT_HTTPHEADER,
                        [
                            "Content-Type: application/json"
                        ]
                    );

                    $firebase_response = curl_exec($ch);
                    $firebase_error = curl_error($ch);

                    curl_close($ch);

                    $message = "Product added successfully.";
                    $message_type = "success";

                    if ($firebase_error !== '') {
                        $message .= " The Firebase copy could not be updated.";
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Clear the form after success
                    |--------------------------------------------------------------------------
                    */

                    $product_name = "";
                    $category_id = 0;
                    $sku = "";
                    $price = "";
                    $description = "";
                    $stock_quantity = "";
                    $image = "";
                    $is_active = 1;

                } else {

                    $message =
                        "Unable to add product: "
                        . mysqli_stmt_error($insert_product);

                    $message_type = "error";
                }

                mysqli_stmt_close($insert_product);
            }
        }

        mysqli_stmt_close($sku_check);
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

    <title>Add Product | Mustbedelah</title>

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

<main class="add-product-container">

    <div class="add-product-heading">

        <h1>Add Product</h1>

        <p>
            Add a new product to the Mustbedelah store.
        </p>

    </div>

    <?php if ($message !== '') { ?>

        <div class="message message-<?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

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
                            $product_name ?? ''
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
                    placeholder="MBD-HOOD-BLK-001"
                    value="<?php
                        echo htmlspecialchars(
                            $sku ?? ''
                        );
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
                    mysqli_data_seek($category_result, 0);
                    ?>

                    <?php while (
                        $category =
                            mysqli_fetch_assoc($category_result)
                    ) { ?>

                        <option
                            value="<?php
                                echo (int)
                                    $category['category_id'];
                            ?>"
                            <?php
                            echo (
                                isset($category_id) &&
                                (int) $category_id ===
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
                    placeholder="0.00"
                    value="<?php
                        echo htmlspecialchars(
                            (string) ($price ?? '')
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
                            (string) ($stock_quantity ?? '')
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
                    placeholder="Describe the product"
                ><?php
                    echo htmlspecialchars(
                        $description ?? ''
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
                    placeholder="images/shirt.jpg"
                    value="<?php
                        echo htmlspecialchars(
                            $image ?? ''
                        );
                    ?>"
                    required
                >

                <small>
                    Example: images/shirt.jpg
                </small>

            </div>

            <div class="form-group form-group-full">

                <label class="active-checkbox">

                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        <?php
                        echo (!isset($is_active) || $is_active === 1)
                            ? 'checked'
                            : '';
                        ?>
                    >

                    <span>
                        <strong>Active product</strong><br>

                        <small>
                            Uncheck this to save the product as hidden.
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
                name="add_product"
                class="form-button"
            >
                <i class="fa-solid fa-plus"></i>
                Add Product
            </button>

        </div>

    </form>

</main>

</body>
</html>