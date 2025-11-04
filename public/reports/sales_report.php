<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();
?>

<?php include '../../includes/header.php'; ?>

<h2>Sales Report</h2>

<?php
try {
    $stmt = $pdo->query("
        SELECT s.sale_id, s.sale_date, c.name AS customer_name, s.total_amount, s.payment_method, s.discount,
               si.product_id, p.name AS product_name, si.quantity, si.unit_price, (si.quantity * si.unit_price) AS subtotal
        FROM sales s
        LEFT JOIN customers c ON s.customer_id = c.customer_id
        JOIN sale_items si ON s.sale_id = si.sale_id
        JOIN products p ON si.product_id = p.product_id
        ORDER BY s.sale_date DESC, s.sale_id DESC
    ");
    $sales = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Failed to fetch sales report: " . htmlspecialchars($e->getMessage()));
}
?>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Sale ID</th>
        <th>Date</th>
        <th>Customer</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Unit Price</th>
        <th>Subtotal</th>
        <th>Total</th>
        <th>Discount</th>
        <th>Payment Method</th>
    </tr>
    <?php foreach ($sales as $sale): ?>
    <tr>
        <td><?= (int)$sale['sale_id'] ?></td>
        <td><?= htmlspecialchars($sale['sale_date']) ?></td>
        <td><?= htmlspecialchars($sale['customer_name'] ?? 'Walk-in') ?></td>
        <td><?= htmlspecialchars($sale['product_name']) ?></td>
        <td><?= (int)$sale['quantity'] ?></td>
        <td><?= CURRENCY ?> <?= number_format($sale['unit_price'], 2) ?></td>
        <td><?= CURRENCY ?> <?= number_format($sale['subtotal'], 2) ?></td>
        <td><?= CURRENCY ?> <?= number_format($sale['total_amount'], 2) ?></td>
        <td><?= CURRENCY ?> <?= number_format($sale['discount'], 2) ?></td>
        <td><?= htmlspecialchars($sale['payment_method']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include '../../includes/footer.php'; ?>
