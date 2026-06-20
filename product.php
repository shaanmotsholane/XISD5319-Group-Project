<?php
session_start();

include 'config/db.php';

if(isset($_GET['error'])){

    echo "<p style='color:red; margin-bottom:20px;'>
            Requested quantity exceeds available stock.
          </p>";
}

$product_id = $_GET['id'];

$sql = "SELECT * FROM products WHERE product_id = '$product_id'";

$result = mysqli_query($conn, $sql);

$product = mysqli_fetch_assoc($result);

$error_message = "";

if(isset($_GET['error']) && $_GET['error'] == "stock"){

    $error_message = "Requested quantity exceeds available stock.";
}
?>

<!DOCTYPE html>
<html>
<head>

    <title><?php echo $product['product_name']; ?></title>
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="product-page">

        <div class="product-image">

            <img src="<?php echo $product['image']; ?>" alt="">

        </div>

        <div class="product-details">

            <h1><?php echo $product['product_name']; ?></h1>

            <p class="price">
                R<?php echo $product['price']; ?>
            </p>

            <p class="stock">
                Available Stock:
                <?php echo $product['stock_quantity']; ?>
            </p>

            <p class="description">
                <?php echo $product['description']; ?>
            </p>

            <?php if($error_message != ""){ ?>

                    <p style="color:red; margin-bottom:20px;">

                        <?php echo $error_message; ?>

                    </p>

            <?php } ?>

            <form method="POST" action="add-to-cart.php">

                <input
                    type="hidden"
                    name="product_id"
                    value="<?php echo $product['product_id']; ?>">

                <label>Size</label>

                <select name="size" required>

                    <option value="">Choose Size</option>

                    <option value="S">S</option>

                    <option value="M">M</option>

                    <option value="L">L</option>

                    <option value="XL">XL</option>

                </select>

                <label>Colour</label>

                <select name="colour" required>

                    <option value="">Choose Colour</option>

                    <option value="Black">Black</option>

                    <option value="White">White</option>

                    <option value="Grey">Grey</option>

                    <option value="Blue">Blue</option>

                </select>

                <br><br>

                <br><br>

                <label>Quantity</label>

                <input
                    type="number"
                    name="quantity"
                    value="1"
                    min="1"
                    max="<?php echo $product['stock_quantity']; ?>">

                <br><br>

                <?php if($product['stock_quantity'] > 0){ ?>

                    <button type="submit">
                        Add To Cart
                    </button>

                <?php } else { ?>

                    <button type="button" disabled>
                        Out Of Stock
                    </button>

                <?php } ?>

            </form>

        </div>

    </div>

</body>

</html>