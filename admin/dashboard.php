<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Admin Dashboard</title>
<link rel="icon" type="image/jpeg" href="../images/logo.jpeg">
<style>

body{
    font-family: Arial;
    background: #f4f4f4;
    padding: 40px;
}

.dashboard{
    width: 500px;
    margin: auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
}

a{
    display: block;
    padding: 15px;
    background: black;
    color: white;
    text-decoration: none;
    margin-top: 10px;
    text-align: center;
}

h1{
    text-align: center;
}

</style>

</head>

<body>
<?php include 'admin-nav.php'; ?>

<div class="dashboard">

    <h1>Admin Dashboard</h1>

    <a href="manage-products.php">
        Manage Products
    </a>

    <a href="add-product.php">
        Add Products
    </a>

    <a href="orders.php">
        View Orders
    </a>

    <a href="messages.php">
        Messages
    </a>

    <a href="logout.php">
        Logout
    </a>

<a href="reports.php">Reports</a>
</div>

</body>
</html>