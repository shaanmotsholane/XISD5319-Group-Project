<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Size Guide | Mustbedelah</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .info-page{max-width:1000px;margin:0 auto;padding:90px 24px 110px;min-height:55vh;line-height:1.8}
        .info-page .eyebrow{letter-spacing:4px;text-transform:uppercase;color:#666;font-size:13px}
        .info-page h1{font-size:clamp(38px,7vw,64px);text-transform:uppercase;margin:10px 0 24px;letter-spacing:-1px}
        .info-page h2{font-size:22px;margin:30px 0 8px}
        .info-page p{color:#444;font-size:16px}
        .size-table-wrap{overflow-x:auto;margin-top:18px}
        .size-table{width:100%;border-collapse:collapse;min-width:520px;text-align:left}
        .size-table th,.size-table td{padding:14px;border-bottom:1px solid #ddd}
        .size-table th{background:#111;color:#fff;text-transform:uppercase;font-size:13px;letter-spacing:1px}
        .size-table td{color:#333}
        .info-note{margin-top:28px;padding:18px;border-left:4px solid #111;background:#f5f5f5}
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<main class="info-page">
    <div class="eyebrow">Help / Fit</div>
    <h1>Size Guide</h1>
    <p>Use this guide as a starting point when choosing your Mustbedelah clothing size. The measurements below are general fit guidance, not confirmed measurements for every product.</p>
    <h2>General clothing guide</h2>
    <div class="size-table-wrap">
        <table class="size-table">
            <thead><tr><th>Size</th><th>General fit</th><th>What to choose</th></tr></thead>
            <tbody>
                <tr><td>XS</td><td>Extra small</td><td>For a closer, smaller fit</td></tr>
                <tr><td>S</td><td>Small</td><td>For a regular slim fit</td></tr>
                <tr><td>M</td><td>Medium</td><td>For a regular fit</td></tr>
                <tr><td>L</td><td>Large</td><td>For a roomier fit</td></tr>
                <tr><td>XL</td><td>Extra large</td><td>For a looser fit</td></tr>
                <tr><td>XXL</td><td>Double extra large</td><td>For an even roomier fit</td></tr>
            </tbody>
        </table>
    </div>
    <h2>How to check your fit</h2>
    <p>Compare the product with a similar item of clothing that fits you well. For a more accurate choice, measure the chest, waist or hips as appropriate and compare those measurements with the measurements provided for the specific product.</p>
    <div class="info-note"><strong>Important:</strong> Oversized and regular-fit items can fit differently. Please check the individual product description or contact us if you need measurements before ordering. Add the brand's actual garment measurements to this page when they are available.</div>
</main>
<?php include 'includes/footer.php'; ?>
</body>
</html>