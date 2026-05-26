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

        <ul class="navlist">

            <li><a href="index.php">Home</a></li>

            <li><a href="shop.php">Shop</a></li>

            <li><a href="about.php">About</a></li>

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
        <form class="contact-form" id="contactForm">

            <input type="text" placeholder="First name" required>

            <input type="text" placeholder="Last name" required>

            <input type="email" placeholder="Email address" required>

            <!-- removed spacing inside textarea -->
            <textarea placeholder="Enter your question or message"></textarea>

            
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
                <li class="Top"><a href="shop.php">Shop</a></li>
                <li id="foot"><a href="shop.php">New Drops</a></li>
                <li id="foot"><a href="shop.php">Hoodies & Tees</a></li>
                <li id="foot"><a href="shop.php">Bottoms</a></li>
            </ul>

            <ul class="list2">
                <li class="Top">Company</li>
                <li id="foot"><a href="about.php">About us</a></li>
                <li id="foot"><a href="about.php#contact">Contact us</a></li>
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