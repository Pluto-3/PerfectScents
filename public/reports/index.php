<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

?>

<?php include '../../includes/header.php'; ?>

<h2>Reports Dashboard</h2>

<?php
try {
    // ---------- SALES SUMMARY ----------
    $stmt = $pdo->query("SELECT COUNT(*) AS total_sales, SUM(total_amount) AS total_revenue FROM sales");
    $sales_summary = $stmt->fetch();

    // ---------- PURCHASES SUMMARY ----------
    $stmt = $pdo->query("SELECT COUNT(*) AS total_purchases, SUM(total_cost) AS total_spent FROM purchases");
    $purchases_summary = $stmt->fetch();

    // ---------- PROFIT MARGIN CALCULATION ----------
    $stmt = $pdo->query("
        SELECT SUM(si.quantity * si.unit_price) AS revenue,
               SUM((pi.cost_per_unit) * si.quantity) AS cost
        FROM sale_items si
        JOIN products p ON si.product_id = p.product_id
        JOIN purchase_items pi ON pi.product_id = si.product_id
    ");
    $profit_data = $stmt->fetch();
    $profit_margin = ($profit_data['revenue'] ?? 0) - ($profit_data['cost'] ?? 0);

    // ---------- MARKETING ROI ----------
    $stmt = $pdo->query("SELECT SUM(budget) AS total_budget, SUM(sales_generated) AS total_sales_generated FROM marketing");
    $marketing_data = $stmt->fetch();
    $roi = ($marketing_data['total_sales_generated'] ?? 0) - ($marketing_data['total_budget'] ?? 0);

    // ---------- LOW STOCK ALERTS ----------
    $stmt = $pdo->prepare("
        SELECT p.name, i.current_stock
        FROM inventory i
        JOIN products p ON i.product_id = p.product_id
        WHERE i.current_stock <= :threshold
        ORDER BY i.current_stock ASC
    ");
    $stmt->execute(['threshold' => STOCK_THRESHOLD]);
    $low_stock_items = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Failed to fetch reports: " . htmlspecialchars($e->getMessage()));
}
?>

<h3>Click here for detailed reports</h3>
<a href="sales_report.php">Sales Report</a>
<a href="purchases_report.php">Purchases Report</a>
<a href="marketing_report.php">Marketing Report</a>
<a href="low_stock.php">Low stock</a>

<h3>Sales Summary</h3>
<p>Total Sales: <?= (int)($sales_summary['total_sales'] ?? 0) ?> | Total Revenue: <?= CURRENCY ?> <?= number_format($sales_summary['total_revenue'] ?? 0, 2) ?></p>

<h3>Purchases Summary</h3>
<p>Total Purchases: <?= (int)($purchases_summary['total_purchases'] ?? 0) ?> | Total Spent: <?= CURRENCY ?> <?= number_format($purchases_summary['total_spent'] ?? 0, 2) ?></p>

<h3>Profit Margin</h3>
<p><?= CURRENCY ?> <?= number_format($profit_margin, 2) ?></p>

<h3>Marketing ROI</h3>
<p>Total Budget: <?= CURRENCY ?> <?= number_format($marketing_data['total_budget'] ?? 0, 2) ?> | Sales Generated: <?= CURRENCY ?> <?= number_format($marketing_data['total_sales_generated'] ?? 0, 2) ?> | ROI: <?= CURRENCY ?> <?= number_format($roi, 2) ?></p>

<h3>Low Stock Alerts (≤ <?= STOCK_THRESHOLD ?> units)</h3>
<?php if (!empty($low_stock_items)): ?>
    <ul>
        <?php foreach ($low_stock_items as $item): ?>
            <li><?= htmlspecialchars($item['name']) ?> - <?= (int)$item['current_stock'] ?> units</li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No low-stock items.</p>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>
