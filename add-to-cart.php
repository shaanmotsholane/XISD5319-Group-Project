<?php
session_start();

include 'config/db.php';

if(!isset($_SESSION['customer_id'])){
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

$product_id = $_POST['product_id'];
$size = $_POST['size'];
$colour = $_POST['colour'];
$quantity = $_POST['quantity'];

/* GET PRODUCT INFORMATION */

$product_sql = "SELECT * FROM products
                WHERE product_id = '$product_id'";

$product_result = mysqli_query($conn, $product_sql);

$product = mysqli_fetch_assoc($product_result);

/* CHECK IF PRODUCT EXISTS */

if(!$product){
    header("Location: shop.php");
    exit();
}

/* CHECK IF PRODUCT IS OUT OF STOCK */

if($product['stock_quantity'] <= 0){

    header("Location: product.php?id=$product_id&error=outofstock");
    exit();
}

/* CHECK IF REQUESTED QUANTITY EXCEEDS STOCK */

if($quantity > $product['stock_quantity']){

    header("Location: product.php?id=$product_id&error=stock");
    exit();
}

/* CHECK IF PRODUCT ALREADY EXISTS IN CART */

$check_cart = "SELECT * FROM cart
               WHERE customer_id = '$customer_id'
               AND product_id = '$product_id'
               AND size = '$size'
               AND colour = '$colour'";

$check_result = mysqli_query($conn, $check_cart);

if(mysqli_num_rows($check_result) > 0){

    $cart_item = mysqli_fetch_assoc($check_result);

    $new_quantity = $cart_item['quantity'] + $quantity;

    /* PREVENT CART QUANTITY FROM EXCEEDING STOCK */

    if($new_quantity > $product['stock_quantity']){

        header("Location: product.php?id=$product_id&error=stock");
        exit();
    }

    $update_quantity = "UPDATE cart
                        SET quantity = '$new_quantity'
                        WHERE cart_id = '{$cart_item['cart_id']}'";

    mysqli_query($conn, $update_quantity);

}else{

    $sql = "INSERT INTO cart(customer_id, product_id, quantity, size,colour)
            VALUES('$customer_id', '$product_id', '$quantity', '$size','$colour')";

    mysqli_query($conn, $sql);
}

header("Location: shop.php?added=1");
exit();
?>