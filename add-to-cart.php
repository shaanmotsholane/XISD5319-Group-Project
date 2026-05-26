<?php
session_start();

include 'config/db.php';

if(!isset($_SESSION['customer_id'])){
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

$product_id = $_GET['id'];

$product_sql = "SELECT * FROM products
                WHERE product_id='$product_id'";

$product_result = mysqli_query($conn, $product_sql);

$product = mysqli_fetch_assoc($product_result);

if($product['stock_quantity'] <= 0){

    echo "Product Out Of Stock";

    exit();
}

$check_cart = "SELECT * FROM cart
               WHERE customer_id='$customer_id'
               AND product_id='$product_id'";

$check_result = mysqli_query($conn, $check_cart);

if(mysqli_num_rows($check_result) > 0){

    $update_quantity = "UPDATE cart
                        SET quantity = quantity + 1
                        WHERE customer_id='$customer_id'
                        AND product_id='$product_id'";

    mysqli_query($conn, $update_quantity);

}else{

    $sql = "INSERT INTO cart(customer_id, product_id, quantity)
            VALUES('$customer_id', '$product_id', 1)";

    mysqli_query($conn, $sql);
}

header("Location: shop.php?added=1");
?>