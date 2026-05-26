<?php
session_start();

include 'config/db.php';

if(!isset($_SESSION['customer_id'])){
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

$sql = "SELECT * FROM cart
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

    $order_sql = "INSERT INTO orders(customer_id, total_amount)
                  VALUES('$customer_id', '$total')";

    mysqli_query($conn, $order_sql);

    foreach($cart_items as $item){

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

    header("Location: order-success.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>

    <style>

        body{
            font-family: Arial;
            background: #f4f4f4;
            padding: 40px;
        }

        .checkout-box{
            background: white;
            width: 400px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
        }

        input, textarea{
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }

        button{
            width: 100%;
            padding: 12px;
            background: black;
            color: white;
            border: none;
            margin-top: 10px;
        }

    </style>
</head>
<body>

<div class="checkout-box">

    <h1>Checkout</h1>

    <h3>Total: R<?php echo $total; ?></h3>

    <form method="POST">

        <input type="text"
               name="phone"
               placeholder="Phone Number"
               required>

        <textarea name="address"
                  placeholder="Delivery Address"
                  required></textarea>

        <button type="submit" name="place_order">
            Place Order
        </button>

    </form>

</div>

</body>
</html>