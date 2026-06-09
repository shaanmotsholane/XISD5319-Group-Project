<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mustbedelah</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
</head>

<body>

    <!-- **********NAVBAR********** -->
    <nav class="navbar">

    <div class="logo">
        <a href="index.php">mustbedelah</a>
    </div>

    <ul class="nav-links">

        <li><a href="index.php">Home</a></li>

        <li><a href="shop.php">Shop</a></li>

        <li><a href="about.php">About</a></li>

        <li><a href="about.php#contact">Contact</a></li>

        <li><a href="cart.php">Cart</a></li>

        <?php if(isset($_SESSION['name'])){ ?>

            <li>
                <a href="#">
                    <?php echo $_SESSION['name']; ?>
                </a>
            </li>

            <li>
                <a href="logout.php">Logout</a>
            </li>

        <?php } else { ?>

            <li>
                <a href="login.php">Sign In</a>
            </li>

        <?php } ?>

    </ul>

</nav>

    <!-- This is for the opening picture  -->
    <section class="hero">

        <div class="placeholder-img"></div>

        <div class="hero-text">

            <h1>FRESH DROPS DAILY</h1>

            <p>
                Bold streetwear for those who make their own rules.<br>
                Rep the culture, own the streets.
            </p>

            <a href="shop.php">
                <button class="shop-btn">
                    Shop Now
                </button>
            </a>

        </div>

    </section>

    <!-- This is for the shop by style section -->
    <section class="style">

        <h2>SHOP BY STYLE</h2>

        <div class="style-grid">

            <!-- FIXED (anchor wrapping properly closed) -->
            <a href="shop.php">
                <div class="card">
                    <img src="images/DSC_0658.JPG" alt="Hoodies">
                    <h3>Hoodies & Jackets</h3>
                    <p>EXPLORE...</p>
                </div>
            </a>

            <a href="shop.php">
                <div class="card">
                    <img src="images/DSC_0634.JPG" alt="Street Culture">
                    <h3>Street Culture</h3>
                    <p>EXPLORE...</p>
                </div>
            </a>

            <a href="shop.php">
                <div class="card">
                    <img src="images/cap.jpeg" alt="Accessories">
                    <h3>Accessories</h3>
                    <p>EXPLORE...</p>
                </div>
            </a>

        </div>

    </section>

    <!-- This is for the born for the streets section -->
    <section class="born">

        <h2>BORN FROM THE <br>STREETS</h2>

        <h4>MUSTBEDELAH</h4>

        <p>
            Mustbedelah is more than just clothing-- Its <br> a movement. We represent the raw energy or <br>
            street culture,The creativity of urban youth <br> and the fearless attitude of those who dare <br>
            to be different <br><br><br>
            Every piece is designed for the rebels <br> ,Creators,the skaters and the haters. We're <br>
            here to amplify your voice and celebrate your <br>individuality.
        </p>

        <img src="images/DSC_0641.JPG" alt="StreetWear Image">

        <button class="ourstory">Our Story</button>

    </section>

    <!--This is for the Join the movement section-->
    <section class="join">

        <h2>JOIN THE MOVEMENT</h2>

        <button class="sub-btn">Subscribe</button>

        <!-- FIXED (email type for validation) -->
        <input type="email" placeholder="Enter your email" id="email_input">

    </section>

    <!-- This is a footer for the website  -->
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