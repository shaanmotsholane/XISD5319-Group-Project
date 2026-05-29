<?php

session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}


include '../config/db.php';

$id = $_GET['id'];

$sql = "SELECT * FROM products
        WHERE product_id='$id'";

$result = mysqli_query($conn, $sql);

$product = mysqli_fetch_assoc($result);

if(isset($_POST['update_product'])){

    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock_quantity = $_POST['stock_quantity'];

    $update = "UPDATE products
               SET product_name='$product_name',
                   price='$price',
                   description='$description',
                   stock_quantity='$stock_quantity'
               WHERE product_id='$id'";

    mysqli_query($conn, $update);

    header("Location: manage-products.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>

    <style>

        body{
            font-family: Arial;
            background: #f4f4f4;
            padding: 40px;
        }

        form{
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

<form method="POST">

    <h1>Edit Product</h1>

    <input type="text"
           name="product_name"
           value="<?php echo $product['product_name']; ?>"
           required>

    <input type="number"
           step="0.01"
           name="price"
           value="<?php echo $product['price']; ?>"
           required>

    <textarea name="description"><?php echo $product['description']; ?></textarea>

    <input type="number"
           name="stock_quantity"
           value="<?php echo $product['stock_quantity']; ?>"
           required>

    <button type="submit" name="update_product">
        Update Product
    </button>

</form>

</body>
</html>