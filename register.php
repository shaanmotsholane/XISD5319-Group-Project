<?php
include 'config/db.php';

if(isset($_POST['register'])){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "INSERT INTO customers(name, email, password)
            VALUES('$name', '$email', '$password')";

    if(mysqli_query($conn, $sql)){

        header("Location: login.php");
        exit();

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="form-container">

    <h1>Create Account</h1>

    <form method="POST">

        <div class="form-group">
            <label>Full Name</label>
            <input
                type="text"
                name="name"
                placeholder="Enter your full name"
                required
            >
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input
                type="email"
                name="email"
                placeholder="Enter your email"
                required
            >
        </div>

        <div class="form-group">
            <label>Password</label>
            <input
                type="password"
                name="password"
                placeholder="Create a password"
                required
            >
        </div>

        <button type="submit" name="register">
            Create Account
        </button>

    </form>

    <p class="form-footer-text">
        Already have an account?
        <a href="login.php">Login Here</a>
    </p>

</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>