<?php
include 'config/db.php';

if(isset($_POST['send_message'])){

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO contact_messages(name, email, message)
            VALUES('$name', '$email', '$message')";

    if(mysqli_query($conn, $sql)){

        echo "Message Sent Successfully";

    }else{

        die("SQL Error: " . mysqli_error($conn));
    }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

    <!-- NAVBAR -->

    <?php include 'includes/header.php'; ?>

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
            <textarea
                name="message"
                placeholder="Enter your question or message"
                required></textarea>

            
            <button
                type="submit"
                name="send_message"
                class="submit-btn">

                Submit

            </button>

        </form>

    </section>

    <!-- This is a footer for the website  -->
    <section>
       <?php include 'includes/footer.php'; ?>
    </section>

    

</body>

</html>