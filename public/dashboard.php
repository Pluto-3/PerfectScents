<?php
require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/session.php';
require_login();

// ===== Summary Metrics =====

// Total Sales
$stmt = $pdo->query("SELECT IFNULL(SUM(total_amount),0) AS total_sales FROM sales");
$total_sales = $stmt->fetch()['total_sales'];

// Total Profit
$stmt = $pdo->query("
    SELECT IFNULL(SUM((si.unit_price - p.cost_price) * si.quantity),0) AS total_profit
    FROM sale_items si
    JOIN products p ON si.product_id = p.product_id
");
$total_profit = $stmt->fetch()['total_profit'];

// Low Stock Items
$stmt = $pdo->prepare("
    SELECT p.name, i.current_stock
    FROM inventory i
    JOIN products p ON i.product_id = p.product_id
    WHERE i.current_stock <= ?
    ORDER BY i.current_stock ASC
");
$stmt->execute([STOCK_THRESHOLD]);
$low_stock_items = $stmt->fetchAll();

// Top Selling Products
$stmt = $pdo->query("
    SELECT p.name, SUM(si.quantity) AS sold_qty
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
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --primary: #009B77;
        --secondary: #F5F5F5;
        --text-main: #222;
        --text-muted: #666;
        --border: #E0E0E0;
        --bg: #FFFFFF;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--bg);
        color: var(--text-main);
        margin: 0;
        padding: 0;
        line-height: 1.6;
    }

    .container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 24px;
    }

    h1, h2 {
        font-weight: 600;
        margin-bottom: 16px;
    }

    h1 {
        font-size: 1.8rem;
    }

    h2 {
        font-size: 1.2rem;
        color: var(--text-muted);
        margin-top: 32px;
    }

    .metrics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
    }

    .card {
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .metric-value {
        font-size: 1.6rem;
        font-weight: 600;
        color: var(--primary);
    }

    .metric-label {
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    ul, ol {
        margin: 0;
        padding-left: 20px;
    }

    li {
        margin-bottom: 6px;
    }

    .alert-card {
        background: var(--secondary);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .alert-card strong {
        color: var(--primary);
    }

    .quick-links {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 24px;
    }

    .quick-links a {
        display: inline-block;
        border: 1px solid var(--primary);
        color: var(--primary);
        text-decoration: none;
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 500;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .quick-links a:hover {
        background: var(--primary);
        color: #fff;
    }

    @media (max-width: 600px) {
        .metrics {
            grid-template-columns: 1fr;
        }
    }
</style>
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="container">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>

    <!-- Summary Metrics -->
    <div class="metrics">
        <div class="card">
            <div class="metric-label">Total Sales</div>
            <div class="metric-value"><?= CURRENCY . ' ' . number_format($total_sales, 2) ?></div>
        </div>
        <div class="card">
            <div class="metric-label">Total Profit</div>
            <div class="metric-value"><?= CURRENCY . ' ' . number_format($total_profit, 2) ?></div>
        </div>
    </div>

    <!-- Alerts & Notifications -->
    <h2>Low Stock Alerts (≤ <?= STOCK_THRESHOLD ?> Units)</h2>
    <?php if($low_stock_items): ?>
        <div class="alert-card">
            <ul>
                <?php foreach($low_stock_items as $item): ?>
                    <li><strong><?= htmlspecialchars($item['name']) ?></strong> : <?= $item['current_stock'] ?> units left</li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <div class="alert-card">
            <p>All products are above threshold.</p>
        </div>
    <?php endif; ?>

    <!-- Performance Highlights -->
    <h2>Top-Selling Products</h2>
    <?php if($top_products): ?>
        <div class="card">
            <ol>
                <?php foreach($top_products as $prod): ?>
                    <li><?= htmlspecialchars($prod['name']) ?> : <?= $prod['sold_qty'] ?> units sold</li>
                <?php endforeach; ?>
            </ol>
        </div>
    <?php else: ?>
        <p>No sales recorded yet.</p>
    <?php endif; ?>

    <!-- Quick Access -->
    <h2>Quick Access</h2>
    <div class="quick-links">
        <a href="<?= BASE_URL ?>../public/sales/">Sales</a>
        <a href="<?= BASE_URL ?>../public/inventory/">Inventory</a>
        <a href="<?= BASE_URL ?>../public/products/">Products</a>
        <a href="<?= BASE_URL ?>../public/reports/">Reports</a>
        <a href="<?= BASE_URL ?>../public/marketing/">Marketing</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>
