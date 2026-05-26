<?php
include '../config/db.php';

$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>

    <style>

        body{
            font-family: Arial;
            background: #f4f4f4;
            padding: 40px;
        }

        table{
            width: 100%;
            background: white;
            border-collapse: collapse;
        }

        th, td{
            border: 1px solid #ccc;
            padding: 15px;
        }

        a{
            text-decoration: none;
        }

        .btn{
            padding: 8px 12px;
            background: black;
            color: white;
        }

    </style>
</head>
<body>

<h1>Manage Products</h1>

<br>

<a class="btn" href="add-product.php">
    Add New Product
</a>

<br><br>

<table>

<tr>
    <th>ID</th>
    <th>Product</th>
    <th>Price</th>
    <th>Stock</th>
    <th>Actions</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<tr>

    <td>
        <?php echo $row['product_id']; ?>
    </td>

    <td>
        <?php echo $row['product_name']; ?>
    </td>

    <td>
        R<?php echo $row['price']; ?>
    </td>

    <td>
        <?php echo $row['stock_quantity']; ?>
    </td>

    <td>

        <a class="btn"
           href="edit-product.php?id=<?php echo $row['product_id']; ?>">
            Edit
        </a>

        <a class="btn"
           href="delete-product.php?id=<?php echo $row['product_id']; ?>">
            Delete
        </a>

    </td>

</tr>

<?php } ?>

</table>

</body>
</html>