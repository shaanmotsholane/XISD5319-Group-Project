<?php

session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include '../config/db.php';

$sql = "SELECT * FROM orders
        INNER JOIN customers
        ON orders.customer_id = customers.customer_id
        ORDER BY order_date DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Orders</title>
    <link rel="icon" type="image/jpeg" href="../images/logo.jpeg">
    <style>

        body{
            font-family: Arial;
            padding: 40px;
            background: #f4f4f4;
        }

        table{
            width: 100%;
            background: white;
            border-collapse: collapse;
        }

        th, td{
            padding: 15px;
            border: 1px solid #ccc;
            text-align: left;
        }

        h1{
            margin-bottom: 20px;
        }

    </style>
</head>
<body>
<?php include 'admin-nav.php'; ?>

<h1>Customer Orders</h1>

<table>

<tr>
    <th>Order ID</th>
    <th>Reference</th>
    <th>Customer Name</th>
    <th>Email</th>
    <th>Items</th>
    <th>Total Amount</th>
    <th>Order Date</th>
    
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<tr>

    <td>
        <?php echo $row['order_id']; ?>
    </td>

    <td>
        <strong>
            <?php echo $row['order_reference']; ?>
        </strong>
    </td>

    <td>
        <?php echo $row['name']; ?>
    </td>

    <td>
        <?php echo $row['email']; ?>
    </td>

    <td>

        <?php

            $items_sql = "SELECT order_items.*, products.product_name
                        FROM order_items
                        INNER JOIN products
                        ON order_items.product_id = products.product_id
                        WHERE order_items.order_id = '{$row['order_id']}'";

            $items_result = mysqli_query($conn, $items_sql);

            while($item = mysqli_fetch_assoc($items_result)){

                    echo $item['product_name'];

                    echo " (" . $item['size'] . ")";

                    echo " × " . $item['quantity'];

                    echo "<br>";
                    }

        ?>

    </td>

    <td>
        R<?php echo $row['total_amount']; ?>
    </td>

    <td>
        <?php echo $row['order_date']; ?>
    </td>

</tr>

<?php } ?>

</table>

</body>
</html>