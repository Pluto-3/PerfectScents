<?php

require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/session.php';

require_login(); 

// Fetch summary metrics

// 1. Total Sales (all time)
$stmt = $pdo->query("SELECT IFNULL(SUM(total_amount),0) as total_sales FROM sales");
$total_sales = $stmt->fetch()['total_sales'];

// 2. Total Profit (all time)
$stmt = $pdo->query("
    SELECT IFNULL(SUM((si.unit_price - p.cost_price) * si.quantity),0) as total_profit
    FROM sale_items si
    JOIN products p ON si.product_id = p.product_id
");
$total_profit = $stmt->fetch()['total_profit'];

// 3. Low Stock Alerts
$stmt = $pdo->prepare("
    SELECT p.name, i.current_stock
    FROM inventory i
    JOIN products p ON i.product_id = p.product_id
    WHERE i.current_stock <= ?
    ORDER BY i.current_stock ASC
");
$stmt->execute([STOCK_THRESHOLD]);
$low_stock_items = $stmt->fetchAll();

// 4. Top-Selling Products (by quantity)
$stmt = $pdo->query("
    SELECT p.name, SUM(si.quantity) as sold_qty
    FROM sale_items si
    JOIN products p ON si.product_id = p.product_id
    GROUP BY si.product_id
    ORDER BY sold_qty DESC
    LIMIT 5
");
$top_products = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= SITE_NAME ?> - Dashboard</title>
<link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>

    <div class="metrics">
        <div>Total Sales: <?= CURRENCY . ' ' . number_format($total_sales, 2) ?></div>
        <div>Total Profit: <?= CURRENCY . ' ' . number_format($total_profit, 2) ?></div>
    </div>

    <h2>Low Stock Alerts (≤ <?= STOCK_THRESHOLD ?> At the moment)</h2>
    <?php if($low_stock_items): ?>
        <ul>
            <?php foreach($low_stock_items as $item): ?>
                <li><?= htmlspecialchars($item['name']) ?> — Stock: <?= $item['current_stock'] ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>All products are above threshold.</p>
    <?php endif; ?>

    <h2>Top-Selling Products</h2>
    <?php if($top_products): ?>
        <ol>
            <?php foreach($top_products as $prod): ?>
                <li><?= htmlspecialchars($prod['name']) ?> — Sold: <?= $prod['sold_qty'] ?></li>
            <?php endforeach; ?>
        </ol>
    <?php else: ?>
        <p>No sales recorded yet.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
