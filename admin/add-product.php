<?php
include '../config/db.php';

if(isset($_POST['add_product'])){

    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock_quantity = $_POST['stock_quantity'];
    $image = $_POST['image'];

    $sql = "INSERT INTO products(product_name, price, description, stock_quantity, image)
            VALUES('$product_name', '$price', '$description', '$stock_quantity', '$image')";

    mysqli_query($conn, $sql);

    echo "Product Added Successfully";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>

    <style>

body{
    font-family: Arial;
    background: #f4f4f4;
    padding: 40px;
}

form{
    background: white;
    padding: 30px;
    width: 400px;
    margin: auto;
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
    cursor: pointer;
}

h1{
    text-align: center;
}

</style>
</head>
<body>

<h1>Add Product</h1>

<form method="POST">

    <input type="text" name="product_name" placeholder="Product Name" required>
    <br><br>

    <input type="number" step="0.01" name="price" placeholder="Price" required>
    <br><br>

    <textarea name="description" placeholder="Description"></textarea>
    <br><br>

    <input type="number" name="stock_quantity" placeholder="Stock Quantity" required>
    <br><br>

    <input type="text" name="image" placeholder="Image Path Example: images/shirt.jpg" required>
    <br><br>

    <button type="submit" name="add_product">
        Add Product
    </button>

</form>

</body>
</html>