<?php
session_start();

include 'config/db.php';

// CART COUNT (Optional if you want it in the header)
$cart_count = 0;
if(isset($_SESSION['customer_id'])){
    $customer_id = $_SESSION['customer_id'];
    $cart_sql = "SELECT SUM(quantity) AS total_items FROM cart WHERE customer_id = '$customer_id'";
    $cart_result = mysqli_query($conn, $cart_sql);
    $cart_data = mysqli_fetch_assoc($cart_result);
    $cart_count = $cart_data['total_items'] ?? 0;
}

// CONTACT FORM SUBMISSION
$message_sent = false;
$message_error = "";

if(isset($_POST['send_message'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO contact_messages(name, email, message) VALUES('$name', '$email', '$message')";

    if(mysqli_query($conn, $sql)){
        $message_sent = true;
    } else {
        $message_error = "There was an error sending your message. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - mustbedelah</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">

    <style>
        /* CONTACT PAGE SPECIFIC STYLES */
        body.contact-page {
            margin: 0;
            padding: 0;
            background-color: #050505;
            color: #ffffff;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            overflow-x: hidden;
        }

        .contact-hero {
            height: 40vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: #000;
            position: relative;
        }

        .contact-hero h1 {
            font-size: clamp(3rem, 6vw, 5rem);
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -2px;
            margin: 0;
            line-height: 0.9;
        }

        .contact-hero p {
            font-size: 1.1rem;
            letter-spacing: 6px;
            color: #888;
            text-transform: uppercase;
            margin-top: 15px;
            font-weight: 300;
        }

        .contact-container {
            max-width: 800px;
            margin: -40px auto 100px auto;
            padding: 0 5%;
            position: relative;
            z-index: 2;
        }

        .contact-form-wrapper {
            background: rgba(15, 15, 15, 0.6);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 50px;
            border: 1px solid rgba(255,255,255,0.05);
            box-shadow: 0 20px 60px rgba(0,0,0,0.9);
        }

        .contact-form-wrapper h2 {
            font-size: 2rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 30px;
            text-align: center;
            color: #f0f0f0;
        }

        .contact-form-wrapper .form-group {
            margin-bottom: 20px;
        }

        .contact-form-wrapper input,
        .contact-form-wrapper textarea {
            width: 100%;
            padding: 16px 20px;
            background: rgba(0,0,0,0.4);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            color: #fff;
            font-size: 1rem;
            font-family: inherit;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
            box-sizing: border-box;
        }

        .contact-form-wrapper input:focus,
        .contact-form-wrapper textarea:focus {
            border-color: rgba(0, 255, 65, 0.3);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.05);
        }

        .contact-form-wrapper textarea {
            min-height: 150px;
            resize: vertical;
        }

        .contact-form-wrapper .submit-btn {
            width: 100%;
            padding: 16px;
            background: #ffffff;
            color: #000000;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .contact-form-wrapper .submit-btn:hover {
            background: #00ff41;
            transform: scale(1.02);
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.2);
        }

        .form-message {
            text-align: center;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            font-weight: 600;
        }

        .form-success {
            background: rgba(0, 255, 65, 0.1);
            border: 1px solid rgba(0, 255, 65, 0.2);
            color: #00ff41;
        }

        .form-error {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid rgba(255, 0, 0, 0.2);
            color: #ff5555;
        }

        @media (max-width: 768px) {
            .contact-form-wrapper { padding: 30px 20px; }
            .contact-hero { height: 30vh; }
        }
    </style>
</head>

<body class="contact-page">

    <!-- NAVBAR -->
    <?php include 'includes/header.php'; ?>

    <!-- HERO -->
    <section class="contact-hero">
        <div class="content">
            <h1>CONTACT<br>THE ARCHIVE</h1>
            <p>GET IN TOUCH</p>
        </div>
    </section>

    <!-- CONTACT FORM -->
    <section class="contact-container">
        <div class="contact-form-wrapper">
            
            <h2>Send a Message</h2>

            <?php if($message_sent): ?>
                <div class="form-message form-success">
                    Your message has been sent successfully. We'll get back to you soon!
                </div>
            <?php elseif(!empty($message_error)): ?>
                <div class="form-message form-error">
                    <?php echo $message_error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="contact.php">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Your Name" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Your Email Address" required>
                </div>
                <div class="form-group">
                    <textarea name="message" placeholder="Your Message..." required></textarea>
                </div>
                <button type="submit" name="send_message" class="submit-btn">Send Message</button>
            </form>

        </div>
    </section>

    <!-- FOOTER -->
    <section>
        <?php include 'includes/footer.php'; ?>
    </section>

</body>
</html>