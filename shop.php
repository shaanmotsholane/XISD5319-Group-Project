<?php
session_start();

include 'config/db.php';

$cart_count = 0;

if(isset($_SESSION['customer_id'])){

    $customer_id = $_SESSION['customer_id'];

    $cart_sql = "SELECT SUM(quantity) AS total_items
                 FROM cart
                 WHERE customer_id = '$customer_id'";

    $cart_result = mysqli_query($conn, $cart_sql);

    $cart_data = mysqli_fetch_assoc($cart_result);

    $cart_count = $cart_data['total_items'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Shop - mustbedelah</title>

    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
</head>

<body>

    <!-- NAVBAR -->

    <nav class="navbar">

        <div class="logo">
            <a href="index.php">mustbedelah</a>
        </div>

        <ul class="nav-links">

            <li><a href="index.php">Home</a></li>

            <li>
                <a href="cart.php">
                    Cart (<?php echo $cart_count; ?>)
                </a>
            </li>

            <li><a href="about.php">About</a></li>

            <li><a href="about.php#contact">Contact</a></li>

        </ul>

    </nav>

    <!-- PAGE TITLE -->

    <section class="shop-title">

        <h1>ALL PRODUCTS</h1>

        <p>FIND YOUR NEXT STREETWEAR ESSENTIAL</p>

    </section>

    <?php
        if(isset($_GET['added'])){
            echo "<p style='color:green;'>Product Added To Cart</p>";
        }
    ?>

    <!-- PRODUCT GRID -->

<section class="product-section">

    <form method="GET"
          action="shop.php"
          class="search-form">

        <input type="text"
               name="search"
               placeholder="Search products">

        <button type="submit">
            Search
        </button>

    </form>

    <div class="product-grid">

            <?php
                include 'config/db.php';

                if(isset($_GET['search'])){

                    $search = $_GET['search'];

                    $sql = "SELECT * FROM products
                    WHERE product_name LIKE '%$search%'";

                }else{

                    $sql = "SELECT * FROM products";
                }
                $result = mysqli_query($conn, $sql);

                while($row = mysqli_fetch_assoc($result)) {
            ?>

            <div class="product-card">
                <img src="<?php echo $row['image']; ?>" alt="">
    
                <h3>
                    <?php echo $row['product_name']; ?>
                </h3>

                <p>
                    R<?php echo $row['price']; ?>
                </p>

                <p>
                    <?php echo $row['description']; ?>
                </p>

                <?php if($row['stock_quantity'] > 0){ ?>

                    <a href="product.php?id=<?php echo $row['product_id']; ?>">
                        <button class="view-btn">
                            View Product
                        </button>
                    </a>

                <?php } else { ?>

                    <button class="out-stock-btn" disabled>
                        Out Of Stock
                    </button>

                <?php } ?>

                <p>
                    Stock: <?php echo $row['stock_quantity']; ?>
                </p>
            </div>

        <?php
        }
        ?>

        </div>

    </section>

    <!-- FOOTER -->
    <section>

        <footer>

            <h5>mustbedelah</h5>

            <ul class="list1">
                <li class="Top">Shop</li>
                <li>New Drops</li>
                <li>Hoodies & Tees</li>
                <li>Bottoms</li>
            </ul>

            <ul class="list2">
                <li class="Top">Company</li>
                <li>About us</li>
                <li>Contact us</li>
            </ul>

            <ul class="list3">
                <li class="Top">Help</li>
                <li>Shipping</li>
                <li>Returns</li>
                <li>Size Guide</li>
            </ul>

        </footer>
    </section>

    <script src="script.js"></script>
</body>

</html>