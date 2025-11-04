<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

?>

<?php include '../../includes/header.php'; ?>

<h2>Purchases Report</h2>

<?php
try {
    $stmt = $pdo->query("
        SELECT p.purchase_id, p.purchase_date, s.name AS supplier_name, p.invoice_number, p.total_cost, p.payment_method,
               pi.product_id, pr.name AS product_name, pi.quantity, pi.cost_per_unit, (pi.quantity * pi.cost_per_unit) AS subtotal
        FROM purchases p
        JOIN suppliers s ON p.supplier_id = s.supplier_id
        JOIN purchase_items pi ON p.purchase_id = pi.purchase_id
        JOIN products pr ON pi.product_id = pr.product_id
        ORDER BY p.purchase_date DESC, p.purchase_id DESC
    ");
    $purchases = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Failed to fetch purchases report: " . htmlspecialchars($e->getMessage()));
}
?>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Purchase ID</th>
        <th>Date</th>
        <th>Supplier</th>
        <th>Invoice</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Cost per Unit</th>
        <th>Subtotal</th>
        <th>Total</th>
        <th>Payment Method</th>
    </tr>
    <?php foreach ($purchases as $purchase): ?>
    <tr>
        <td><?= (int)$purchase['purchase_id'] ?></td>
        <td><?= htmlspecialchars($purchase['purchase_date']) ?></td>
        <td><?= htmlspecialchars($purchase['supplier_name']) ?></td>
        <td><?= htmlspecialchars($purchase['invoice_number'] ?? '-') ?></td>
        <td><?= htmlspecialchars($purchase['product_name']) ?></td>
        <td><?= (int)$purchase['quantity'] ?></td>
        <td><?= CURRENCY ?> <?= number_format($purchase['cost_per_unit'], 2) ?></td>
        <td><?= CURRENCY ?> <?= number_format($purchase['subtotal'], 2) ?></td>
        <td><?= CURRENCY ?> <?= number_format($purchase['total_cost'], 2) ?></td>
        <td><?= htmlspecialchars($purchase['payment_method']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include '../../includes/footer.php'; ?>
