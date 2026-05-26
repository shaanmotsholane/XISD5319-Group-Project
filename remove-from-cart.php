<?php

include 'config/db.php';

$cart_id = $_GET['id'];

$sql = "DELETE FROM cart WHERE cart_id='$cart_id'";

mysqli_query($conn, $sql);

header("Location: cart.php");

?>