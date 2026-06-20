<?php
session_start();

include 'config/db.php';

$cart_id = $_GET['cart_id'];
$action = $_GET['action'];

/* GET CURRENT CART ITEM */

$cart_sql = "SELECT cart.*, products.stock_quantity
             FROM cart
             INNER JOIN products
             ON cart.product_id = products.product_id
             WHERE cart.cart_id = '$cart_id'";

$cart_result = mysqli_query($conn, $cart_sql);

$cart_item = mysqli_fetch_assoc($cart_result);

$current_quantity = $cart_item['quantity'];
$stock_quantity = $cart_item['stock_quantity'];

if($action == 'increase'){

    if($current_quantity < $stock_quantity){

        $sql = "UPDATE cart
                SET quantity = quantity + 1
                WHERE cart_id = '$cart_id'";

        mysqli_query($conn, $sql);
    }

}else if($action == 'decrease'){

    if($current_quantity > 1){

        $sql = "UPDATE cart
                SET quantity = quantity - 1
                WHERE cart_id = '$cart_id'";

        mysqli_query($conn, $sql);
    }
}

header("Location: cart.php");
exit();
?>