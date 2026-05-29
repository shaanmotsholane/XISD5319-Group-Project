<?php

session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include '../config/db.php';

$id = $_GET['id'];

$sql = "DELETE FROM products
        WHERE product_id='$id'";

mysqli_query($conn, $sql);

header("Location: manage-products.php");

?>