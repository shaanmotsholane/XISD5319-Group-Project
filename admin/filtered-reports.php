<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
require '../config/db.php';
$period = $_GET['period'] ?? 'today';
$today = new DateTimeImmutable('today');
$start = $today; $end = $today->modify('+1 day');
switch ($period) {
 case 'week': $start=$today->modify('monday this week'); $end=$start->modify('+7 days'); break;
 case 'month': $start=$today->modify('first day of this month'); $end=$start->modify('+1 month'); break;
 case 'custom':
    $from=$_GET['from'] ?? ''; $to=$_GET['to'] ?? '';
    $a=DateTimeImmutable::createFromFormat('!Y-m-d', $from);
    $b=DateTimeImmutable::createFromFormat('!Y-m-d', $to);
    if (!$a || !$b || $a->format('Y-m-d')!==$from || $b->format('Y-m-d')!==$to || $a>$b) { http_response_code(400); exit('Select a valid date range.'); }
    $start=$a; $end=$b->modify('+1 day'); break;
 case 'today': break;
 default: http_response_code(400); exit('Invalid report period.');
}
$startSql=$start->format('Y-m-d H:i:s'); $endSql=$end->format('Y-m-d H:i:s');
$sql="SELECT COUNT(*) AS order_count, COALESCE(SUM(total_amount),0) AS order_value, COALESCE(SUM(CASE WHEN status = 'Delivered' THEN total_amount ELSE 0 END),0) AS delivered_value, SUM(status = 'Order Placed') AS placed, SUM(status = 'Preparing') AS preparing, SUM(status = 'On the Way') AS dispatched, SUM(status = 'Delivered') AS delivered, SUM(status = 'Cancelled') AS cancelled FROM orders WHERE order_date >= ? AND order_date < ?";
$stmt=mysqli_prepare($conn,$sql); mysqli_stmt_bind_param($stmt,'ss',$startSql,$endSql); mysqli_stmt_execute($stmt); $totals=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
$sql='SELECT order_reference,order_date,total_amount,status FROM orders WHERE order_date >= ? AND order_date < ? ORDER BY order_date DESC';
$stmt=mysqli_prepare($conn,$sql); mysqli_stmt_bind_param($stmt,'ss',$startSql,$endSql); mysqli_stmt_execute($stmt); $orders=mysqli_stmt_get_result($stmt);
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width,initial-scale=1">
      <title>Filtered Reports | Mustbedelah</title>
      <link rel="stylesheet" href="admin.css">
      <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
      <style>.filter-report{max-width:1100px;margin:35px auto;padding:20px}.filter-report form{display:flex;gap:12px;flex-wrap:wrap;align-items:end;margin:22px 0}.filter-report input,.filter-report select{padding:10px;border:1px solid #aaa;border-radius:5px}.filter-report button{background:#111;color:white;padding:11px 18px;border:0;border-radius:5px}.filter-report button:hover{background:#444}.filter-report table{width:100%;border-collapse:collapse;background:white}.filter-report td,.filter-report th{padding:12px;border-bottom:1px solid #ddd;text-align:left}.filter-report .metrics{display:flex;gap:16px;flex-wrap:wrap}.filter-report .metric{background:#fff;padding:18px;border:1px solid #ddd;border-radius:8px;flex:1;min-width:160px}</style>
   </head>
   <body>
      <?php include 'admin-nav.php'; ?>
      <main class="filter-report"><h1>Filtered Order Reports</h1><p>Order value is not verified payment revenue. Delivered value is based on the order status.</p>
         <form method="get">
            <label>Period <select name="period">
               <option value="today" <?= $period==='today'?'selected':'' ?>>Today</option>
               <option value="week" <?= $period==='week'?'selected':'' ?>>This week</option>
               <option value="month" <?= $period==='month'?'selected':'' ?>>This month</option>
               <option value="custom" <?= $period==='custom'?'selected':'' ?>>Custom dates</option>
            </select></label><label>From <input type="date" name="from" value="<?= e($_GET['from'] ?? $today->format('Y-m-d')) ?>"></label>
            <label>To <input type="date" name="to" value="<?= e($_GET['to'] ?? $today->format('Y-m-d')) ?>"></label>
            <button type="submit">Apply Filter</button>
         </form>
            <p>Showing <?= e($start->format('d M Y')) ?> to <?= e($end->modify('-1 day')->format('d M Y')) ?></p>
            <div class="metrics"><div class="metric"><b>Orders</b><h2><?= (int)$totals['order_count'] ?></h2></div><div class="metric"><b>Total order value</b><h2>R <?= number_format((float)$totals['order_value'],2) ?></h2></div><div class="metric"><b>Delivered order value</b><h2>R <?= number_format((float)$totals['delivered_value'],2) ?></h2></div></div>
            <p style="margin:22px 0">Placed: <?= (int)$totals['placed'] ?> | Preparing: <?= (int)$totals['preparing'] ?> | On the Way: <?= (int)$totals['dispatched'] ?> | Delivered: <?= (int)$totals['delivered'] ?> | Cancelled: <?= (int)$totals['cancelled'] ?></p>
         <div style="overflow-x:auto"><table><thead><tr><th>Reference</th><th>Date</th><th>Status</th><th>Order value</th></tr></thead><tbody><?php while($order=mysqli_fetch_assoc($orders)): ?><tr><td><?= e($order['order_reference']) ?></td><td><?= e($order['order_date']) ?></td><td><?= e($order['status']) ?></td><td>R <?= number_format((float)$order['total_amount'],2) ?></td></tr><?php endwhile; ?></tbody></table></div>
      </main>
   </body>
</html>
