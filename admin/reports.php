<?php
session_start();
if(!isset($_SESSION['admin'])){ header("Location: login.php"); exit(); }
include '../config/db.php';
$p=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM products"));
$c=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM customers"));
$o=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM orders"));
$m=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM contact_messages"));
$r=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COALESCE(SUM(total_amount),0) total FROM orders"));
?>
<!DOCTYPE html><html><head><title>Reports</title>
<link rel="icon" type="image/jpeg" href="../images/logo.jpeg">
<style>body{font-family:Arial;background:#f4f4f4;padding:40px}.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px}.card{background:#fff;padding:25px;border-radius:12px;font-size:22px}</style>
</head><body><?php include 'admin-nav.php'; ?>
<h1>Reports</h1><div class="cards">
<div class="card">Products: <?php echo $p['total']; ?></div>
<div class="card">Customers: <?php echo $c['total']; ?></div>
<div class="card">Orders: <?php echo $o['total']; ?></div>
<div class="card">Messages: <?php echo $m['total']; ?></div>
<div class="card">Revenue: R<?php echo $r['total']; ?></div>
</div></body></html>