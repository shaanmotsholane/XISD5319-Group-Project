<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping | Mustbedelah</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .info-page{max-width:900px;margin:0 auto;padding:90px 24px 110px;min-height:55vh;line-height:1.8}
        .info-page .eyebrow{letter-spacing:4px;text-transform:uppercase;color:#666;font-size:13px}
        .info-page h1{font-size:clamp(38px,7vw,64px);text-transform:uppercase;margin:10px 0 24px;letter-spacing:-1px}
        .info-page h2{font-size:22px;margin:30px 0 8px}
        .info-page p,.info-page li{color:#444;font-size:16px}
        .info-page ul{padding-left:22px;margin-top:10px}
        .info-note{margin-top:28px;padding:18px;border-left:4px solid #111;background:#f5f5f5}
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<main class="info-page">
    <div class="eyebrow">Help / Delivery</div>
    <h1>Shipping</h1>
    <p>We want your Mustbedelah order to reach you safely. Please check that your delivery details are correct when placing an order.</p>
    <h2>Order processing</h2>
    <p>Orders are prepared after they have been placed and confirmed. Processing and delivery times may vary depending on product availability and your delivery area.</p>
    <h2>Delivery details</h2>
    <ul>
        <li>Enter a complete delivery address and a working contact number at checkout.</li>
        <li>Make sure someone can receive the parcel at the delivery address.</li>
        <li>If your address or contact details change, contact us as soon as possible.</li>
    </ul>
    <h2>Delivery time and fees</h2>
    <p>Delivery timeframes and any shipping fees should be confirmed at checkout or with the Mustbedelah team before an order is finalised. We will communicate any delays where possible.</p>
    <div class="info-note"><strong>Need help?</strong><br>Contact us through our <a href="about.php#final-section" style="text-decoration:underline">contact page</a> and include your order details if you have already placed an order.</div>
</main>
<?php include 'includes/footer.php'; ?>
</body>
</html>