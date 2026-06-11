<?php
include 'config/db.php';

if(isset($_POST['register'])){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "INSERT INTO customers(name, email, password)
            VALUES('$name', '$email', '$password')";

    if(mysqli_query($conn, $sql)){
        echo "Registration Successful";
    }else{
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
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

<h1>Register</h1>

<form method="POST">

    <input type="text" name="name" placeholder="Full Name" required>

    <input type="email" name="email" placeholder="Email" required>

    <input type="password" name="password" placeholder="Password" required>

    <button type="submit" name="register">
        Register
    </button>

</form>

</body>
</html>