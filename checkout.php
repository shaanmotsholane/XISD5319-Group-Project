<?php
session_start();

include 'config/db.php';

if(!isset($_SESSION['customer_id'])){
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

$check_cart = "SELECT * FROM cart
               WHERE customer_id = '$customer_id'";

$cart_result = mysqli_query($conn, $check_cart);

if(mysqli_num_rows($cart_result) == 0){

    header("Location: cart.php?error=empty");

    exit();
}

$sql = "SELECT
            cart.cart_id,
            cart.product_id,
            cart.quantity,
            cart.size,
            cart.colour,
            products.product_name,
            products.price,
            products.image,
            products.stock_quantity
        FROM cart
        INNER JOIN products
        ON cart.product_id = products.product_id
        WHERE cart.customer_id = '$customer_id'";

$result = mysqli_query($conn, $sql);

$total = 0;

$cart_items = [];

while($row = mysqli_fetch_assoc($result)){

    $total += $row['price'] * $row['quantity'];

    $cart_items[] = $row;
}

if(isset($_POST['place_order'])){

    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $update_customer = "UPDATE customers
                        SET phone='$phone',
                            address='$address'
                        WHERE customer_id='$customer_id'";

    mysqli_query($conn, $update_customer);

    $order_reference = "MBD-" . strtoupper(substr(md5(uniqid()), 0, 8));

    $order_sql = "INSERT INTO orders(customer_id, order_reference, total_amount)
                    VALUES('$customer_id', '$order_reference', '$total')";

    mysqli_query($conn, $order_sql);

    $order_id = mysqli_insert_id($conn);

    foreach($cart_items as $item){

    $product_id = $item['product_id'];

    $quantity = $item['quantity'];

    $size = $item['size'];

    $colour = $item['colour'];

    $price = $item['price'];

    $order_item_sql = "INSERT INTO order_items(
                        order_id,
                        product_id,
                        quantity,
                        size,
                        colour,
                        price
                    )
                    VALUES(
                        '$order_id',
                        '$product_id',
                        '$quantity',
                        '$size',
                        '$colour',
                        '$price'
                    )";

    mysqli_query($conn, $order_item_sql);

    $product_id = $item['product_id'];

    $quantity = $item['quantity'];

    $update_stock = "UPDATE products
                     SET stock_quantity = stock_quantity - $quantity
                     WHERE product_id='$product_id'";

    mysqli_query($conn, $update_stock);
    }

    $clear_cart = "DELETE FROM cart
                   WHERE customer_id='$customer_id'";

    mysqli_query($conn, $clear_cart);

    header("Location: order-success.php?ref=" . $order_reference);
exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>

body{
    font-family: Arial, sans-serif;
    background: #f8f8f8;
}

/* CHECKOUT WRAPPER */

.checkout-wrapper{
    width: 90%;
    max-width: 600px;

    margin: 120px auto;

    background: white;

    padding: 40px;

    border-radius: 12px;
}

/* TITLES */

.checkout-wrapper h1{
    font-size: 40px;
    margin-bottom: 20px;
}

.checkout-wrapper h3{
    margin-bottom: 30px;
    font-size: 22px;
}

/* FORM */

.checkout-form{
    display: flex;
    flex-direction: column;
}

.checkout-form input,
.checkout-form textarea{

    width: 100%;

    padding: 15px;

    margin-bottom: 20px;

    border: 1px solid #ddd;

    font-size: 15px;

    outline: none;
}

.checkout-form textarea{
    min-height: 120px;
    resize: none;
}

/* BUTTON */

.checkout-btn{

    padding: 15px;

    background: black;

    color: white;

    border: none;

    cursor: pointer;

    font-size: 16px;

    transition: 0.3s;
}

.checkout-btn:hover{
    background: #333;
}

/* CHECKOUT ITEMS */

.checkout-item{
    display: flex;
    align-items: center;

    gap: 20px;

    padding: 15px;

    margin-bottom: 20px;

    background: #f8f8f8;

    border-radius: 10px;
}

.checkout-image{
    width: 80px;
    height: 80px;

    object-fit: cover;

    border-radius: 10px;
}

.checkout-info{
    flex: 1;
}

.checkout-info h4{
    margin-bottom: 8px;
}

.checkout-info p{
    margin-bottom: 5px;
    color: #555;
}

/* PAYMENT */

.checkout-form select{
    width: 100%;

    padding: 15px;

    margin-bottom: 20px;

    border: 1px solid #ddd;

    font-size: 15px;
}

.card-row{
    display: flex;
    gap: 15px;
}

.card-row input{
    flex: 1;
}/* CHECKOUT ITEMS */

.checkout-item{
    display: flex;
    align-items: center;

    gap: 20px;

    padding: 15px;

    margin-bottom: 20px;

    background: #f8f8f8;

    border-radius: 10px;
}

.checkout-image{
    width: 80px;
    height: 80px;

    object-fit: cover;

    border-radius: 10px;
}

.checkout-info{
    flex: 1;
}

.checkout-info h4{
    margin-bottom: 8px;
}

.checkout-info p{
    margin-bottom: 5px;
    color: #555;
}

/* PAYMENT */

.checkout-form select{
    width: 100%;

    padding: 15px;

    margin-bottom: 20px;

    border: 1px solid #ddd;

    font-size: 15px;
}

.card-row{
    display: flex;
    gap: 15px;
}

.card-row input{
    flex: 1;
}

</style>
</head>
<body>

<div class="checkout-wrapper">

    <h1>Checkout</h1>

    <h3>
        Total: R<?php echo $total; ?>
    </h3>

    <?php foreach($cart_items as $item){ ?>

    <div class="checkout-item">

        <img
            src="<?php echo $item['image']; ?>"
            alt=""
            class="checkout-image">

        <div class="checkout-info">

            <h4><?php echo $item['product_name']; ?></h4>

            <p>Size: <?php echo $item['size']; ?></p>

            <p>Colour: <?php echo $item['colour']; ?></p>

            <p>Quantity: <?php echo $item['quantity']; ?></p>

            <p>
                R<?php echo $item['price'] * $item['quantity']; ?>
            </p>

        </div>

    </div>

    <?php } ?>

    <form method="POST" class="checkout-form">

        <input type="text"
               name="phone"
               placeholder="Phone Number"
               required>

        <textarea name="address"
                  placeholder="Delivery Address"
                  required></textarea>
        
        <select name="payment_method" required>

    <option value="">Select Payment Method</option>

    <option value="Credit Card">Credit Card</option>

    <option value="Debit Card">Debit Card</option>

    <option value="EFT">EFT</option>

</select>

<input
    type="text"
    name="card_name"
    placeholder="Name On Card"
    required>

<input
    type="text"
    name="card_number"
    placeholder="Card Number"
    maxlength="16"
    required>

<div class="card-row">

    <input
        type="text"
        name="expiry"
        placeholder="MM/YY"
        maxlength="5"
        required>

    <input
        type="text"
        name="cvv"
        placeholder="CVV"
        maxlength="3"
        required>

</div>

        <button type="submit"
                name="place_order"
                class="checkout-btn">

            Place Order

        </button>

    </form>

</div>

</body>
</html>