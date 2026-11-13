<?php
include 'config/db.php';

$error = "";

if(isset($_POST['register'])){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password']?? '';

    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $error = "Password must contain an uppercase letter.";
    }

    if (!preg_match('/[a-z]/', $password)) {
        $error = "Password must contain a lowercase letter.";
    }

    if (!preg_match('/[0-9]/', $password)) {
        $error = "Password must contain a number.";
    }

    if ($password !== $confirm_password){
        $error = "Passwords do not match";
    }

    if (
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !preg_match('/\.(com|net|org|co\.za)$/i', $email)
    ){
        $error = "Please enter a valid email address.";
    }

    if (empty($error)){
        $sql = "INSERT INTO customers(name, email, password)
            VALUES('$name', '$email', '$password')";

        if(mysqli_query($conn, $sql)){

            header("Location: login.php");
            exit();

        }else{
            echo "Error: " . mysqli_error($conn);
        }

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

    <?php if (!empty($error)): ?>

        <p style="color: red;">
            <?php echo htmlspecialchars($error); ?>
        </p>

    <?php endif; ?>

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
            <!-- This has to be validated so that some how you can know which emails are allowed or -->
            <!-- we can even try to send an opt to the person if they put in the otp we can give or grant them access to the website -->
            <label>Email Address</label>
            <input
                type="email"
                name="email"
                placeholder="Enter your email"
                required 
                pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|net|org|co\.za)$"
            >
        </div>

        <div class="form-group">
            <!-- Making the password atleast be a bit harder that before -->
            <!-- Using atleast 8 characters ,including uppercase ,lowercase and a numbere -->
            <label>Password</label>
            <input
                type="password"
                name="password"
                placeholder="Create a password"
                minlength = "8"
                required
                pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]){8,}"
                title="Use atleast 8 characters ,including uppercase ,lowercase and a number"
            >
        </div>

        <div class="form-group">
            <label>Confirm Password</label>
            <input
                type="password"
                name="confirm_password"
                placeholder="Enter your password again"
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