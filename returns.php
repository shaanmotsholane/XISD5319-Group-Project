<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returns | Mustbedelah</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .info-page{max-width:900px;margin:0 auto;padding:90px 24px 110px;min-height:55vh;line-height:1.8}
        .info-page .eyebrow{letter-spacing:4px;text-transform:uppercase;color:#666;font-size:13px}
        .info-page h1{font-size:clamp(38px,7vw,64px);text-transform:uppercase;margin:10px 0 24px;letter-spacing:-1px}
        .info-page h2{font-size:22px;margin:30px 0 8px}
        .info-page p,.info-page li{color:#444;font-size:16px}
        .info-page ul,.info-page ol{padding-left:22px;margin-top:10px}
        .info-note{margin-top:28px;padding:18px;border-left:4px solid #111;background:#f5f5f5}
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<main class="info-page">
    <div class="eyebrow">Help / Orders</div>
    <h1>Returns</h1>
    <p>If there is a problem with an item you received, please contact the Mustbedelah team so we can review it with you.</p>
    <h2>Before requesting a return</h2>
    <ul>
        <li>Keep the item, packaging and proof of purchase where possible.</li>
        <li>Do not wear, wash or alter the item if you believe it is faulty or incorrect.</li>
        <li>Explain the issue and include your order number, if available.</li>
    </ul>
    <h2>How to contact us</h2>
    <ol>
        <li>Visit our <a href="about.php#final-section" style="text-decoration:underline">contact page</a>.</li>
        <li>Provide your name, email address and order details.</li>
        <li>Describe the issue. If relevant, keep clear photos of the item to help explain the problem.</li>
    </ol>
    <h2>Return eligibility and refunds</h2>
    <p>Return eligibility, time limits, return delivery costs and any refund or exchange arrangements must be confirmed with Mustbedelah before sending an item back. Please contact us first for instructions.</p>
    <div class="info-note"><strong>Please note:</strong> This page is a basic store information page. Confirm the final return, exchange and refund policy with the business before accepting customer orders.</div>
</main>
<?php include 'includes/footer.php'; ?>
</body>
</html>