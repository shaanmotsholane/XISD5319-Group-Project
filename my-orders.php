<?php
session_start();

if(!isset($_SESSION['customer_id'])){
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$customer_id = $_SESSION['customer_id'];

$sql = "SELECT *
        FROM orders
        WHERE customer_id = '$customer_id'
        ORDER BY order_date DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>

    <title>My Orders</title>

    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>

        body{
            font-family: Arial, sans-serif;
            background: #f8f8f8;
            padding: 40px;
        }

        .order-card{

            background: white;

            padding: 25px;

            margin-bottom: 20px;

            border-radius: 12px;

            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .order-card h3{
            margin-bottom: 15px;
        }

        .order-items{
            margin-top: 15px;
        }

        .order-items p{
            margin-bottom: 8px;
        }

    </style>

</head>

<body>
    <!-- **********NAVBAR********** -->
    <nav class="navbar">

    <div class="logo">
        <a href="index.php">mustbedelah</a>
    </div>

    </nav>

    <br>
    
<h1>My Orders</h1>

<?php while($order = mysqli_fetch_assoc($result)){ ?>

<div class="order-card">

    <h3>
        Reference:
        <?php echo $order['order_reference']; ?>
    </h3>

    <p>
        Date: <?php echo $order['order_date']; ?>
    </p>

    <p>
        Total: R<?php echo $order['total_amount']; ?>
    </p>

    <div class="order-items">

        <?php

        $order_id = $order['order_id'];

        $items_sql = "SELECT order_items.*, products.product_name
                      FROM order_items
                      INNER JOIN products
                      ON order_items.product_id = products.product_id
                      WHERE order_items.order_id = '$order_id'";

        $items_result = mysqli_query($conn, $items_sql);

        while($item = mysqli_fetch_assoc($items_result)){

            echo "<p>";

            echo $item['product_name'];

            echo " - Size: " . $item['size'];

            echo " × " . $item['quantity'];

            echo "</p>";
        }

        ?>

    </div>

</div>

<?php } ?>

</body>
</html>