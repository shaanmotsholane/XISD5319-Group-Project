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
    font-family: Arial, sans-serif;
    background: #f8f8f8;
}

/* PAGE TITLE */

.cart-title{
    text-align: center;
    padding: 120px 20px 40px;
}

.cart-title h1{
    font-size: 45px;
    letter-spacing: 2px;
}

/* CART CONTAINER */

.cart-container{
    width: 90%;
    max-width: 1100px;
    margin: auto;
}

/* CART ITEM */

.cart-item{
    background: white;
    display: flex;
    justify-content: space-between;
    align-items: center;

    padding: 25px;

    margin-bottom: 20px;

    border-radius: 10px;
}

/* PRODUCT INFO */

.cart-info h3{
    font-size: 22px;
    margin-bottom: 10px;
}

.cart-info p{
    margin-bottom: 8px;
}

/* BUTTONS */

.cart-actions a button{
    padding: 10px 20px;
    border: none;
    background: black;
    color: white;
    cursor: pointer;
    transition: 0.3s;
}

.cart-actions a button:hover{
    background: #333;
}

/* TOTAL SECTION */

.cart-total{
    background: white;
    padding: 30px;
    border-radius: 10px;
    text-align: right;
    margin-top: 30px;
}

.cart-total h2{
    margin-bottom: 20px;
}

.checkout-btn{
    padding: 15px 30px;
    border: none;
    background: black;
    color: white;
    cursor: pointer;
    transition: 0.3s;
}

.checkout-btn:hover{
    background: #333;
}

</style>
</head>
<body>

<div class="cart-title">
    <h1>Your Cart</h1>
</div>

<div class="cart-container">

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<div class="cart-item">

    <div class="cart-info">

        <h3>
            <?php echo $row['product_name']; ?>
        </h3>

        <p>
            Price: R<?php echo $row['price']; ?>
        </p>

        <p>
            Quantity: <?php echo $row['quantity']; ?>
        </p>

    </div>

    <div class="cart-actions">

        <a href="remove-from-cart.php?id=<?php echo $row['cart_id']; ?>">
            <button>Remove</button>
        </a>

    </div>

</div>

<?php
$total += $row['price'];
}
?>

<div class="cart-total">

    <h2>
        Total: R<?php echo $total; ?>
    </h2>

    <a href="checkout.php">

        <button class="checkout-btn">
            Proceed To Checkout
        </button>

    </a>

</div>

</div>

</body>
</html>