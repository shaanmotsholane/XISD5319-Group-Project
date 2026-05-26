<?php
session_start();

include 'config/db.php';

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM customers 
            WHERE email='$email' 
            AND password='$password'";

    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){

        $row = mysqli_fetch_assoc($result);

        $_SESSION['customer_id'] = $row['customer_id'];
        $_SESSION['name'] = $row['name'];

        header("Location: index.php");

    }else{
        echo "Invalid Email or Password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

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

        input{
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

        h1{
            text-align: center;
        }

    </style>
</head>
<body>

<h1>Login</h1>

<form method="POST">

    <input type="email" name="email" placeholder="Email" required>

    <input type="password" name="password" placeholder="Password" required>

    <button type="submit" name="login">
        Login
    </button>

    <a href="register.php">Or register here</a>

</form>

</body>
</html>