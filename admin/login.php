<?php
session_start();

include '../config/db.php';

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admins
            WHERE username='$username'
            AND password='$password'";

    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){

        $_SESSION['admin'] = $username;

        header("Location: dashboard.php");

    }else{
        echo "Invalid Admin Credentials";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>

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

    </style>
</head>
<body>

<form method="POST">

    <h1>Admin Login</h1>

    <input type="text"
           name="username"
           placeholder="Username"
           required>

    <input type="password"
           name="password"
           placeholder="Password"
           required>

    <button type="submit" name="login">
        Login
    </button>

</form>

</body>
</html>