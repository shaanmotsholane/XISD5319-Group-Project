<?php
include 'config/db.php';

if(isset($_POST['send_message'])){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $sql = "INSERT INTO contact_messages(name, email, message)
            VALUES('$name', '$email', '$message')";

    mysqli_query($conn, $sql);

    echo "Message Sent Successfully";
}
?>

<!DOCTYPE html> 
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - mustbedelah</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">

</head>

<body>

    <!-- NAVBAR -->

    <nav class="navbar">

        <div class="logo">
            <a href="index.php">
                mustbedelah
            </a>
        </div>

        <ul class="nav-links">

            <li><a href="index.php">Home</a></li>

            <li><a href="shop.php">Shop</a></li>

            <li><a href="about.php#contact">
                    Contact
                </a></li>

        </ul>

    </nav>

    <!-- ABOUT SECTION -->

    <section class="about-page" id="about">

        <div class="about-text">

            <h1>
                FROM THE STREETS,
                FOR THE STREETS
            </h1>

            <p>

                MUSTBEDELAH was born in 2016
                from the streets of Soshanguve.

                We wanted to create something
                authentic — clothing that truly
                represents our culture, our
                community and our lifestyle.

                This brand is built on
                confidence, creativity and
                street identity.

            </p>

        </div>

        <div class="about-image">

            <img src="images/About.jpeg" alt="About Image">

        </div>

    </section>

    <!-- CONTACT SECTION -->

    <section class="contact-page" id="contact">

        <h2>Contact Me</h2>

        <!-- method + proper button type -->
        <form class="contact-form" id="contactForm" method="POST">

            <input type="text" placeholder="First name" required name = "name" >

            <input type="text" placeholder="Last name" required name = "lastname">

            <input type="email" placeholder="Email address" required name = "email">

            <!-- removed spacing inside textarea -->
            <textarea name = "mesage" placeholder="Enter your question or message"></textarea>

            
            <button type="submit" class="submit-btn">
                Submit
            </button>

        </form>

    </section>

    <!-- FOOTER -->
    <section>

        <footer>

            <h5>mustbedelah</h5>

            <ul class="list1">
                <li class="Top">Shop</li>
                <li>New Drops</li>
                <li>Hoodies & Tees</li>
                <li>Bottoms</li>
            </ul>

            <ul class="list2">
                <li class="Top">Company</li>
                <li>About us</li>
                <li>Contact us</li>
            </ul>

            <ul class="list3">
                <li class="Top">Help</li>
                <li>Shipping</li>
                <li>Returns</li>
                <li>Size Guide</li>
            </ul>

        </footer>
    </section>

    
    <script src="script.js"></script>

</body>

</html>