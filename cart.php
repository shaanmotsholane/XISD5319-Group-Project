<?php
session_start();

include 'config/db.php';

if(!isset($_SESSION['customer_id'])){
    header("Location: login.php");
}

$customer_id = $_SESSION['customer_id'];

$sql = "SELECT * FROM cart
        INNER JOIN products
        ON cart.product_id = products.product_id
        WHERE cart.customer_id = '$customer_id'";

$result = mysqli_query($conn, $sql);

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>

    <style>

        body{
            font-family: Arial;
            padding: 40px;
        }

        .cart-item{
            border-bottom: 1px solid #ccc;
            padding: 20px;
        }

    </style>
</head>
<body>

<h1>Your Cart</h1>

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<div class="cart-item">

    <h3>
        <?php echo $row['product_name']; ?>
    </h3>

    <p>
        R<?php echo $row['price']; ?>
    </p>

    <a href="remove-from-cart.php?id=<?php echo $row['cart_id']; ?>">
        <button>Remove</button>
    </a>
</div>

<?php
$total += $row['price'];
}
?>

<h2>
    Total: R<?php echo $total; ?>
</h2>

<a href="checkout.php">
    <button>Checkout</button>
</a>

</body>
</html>