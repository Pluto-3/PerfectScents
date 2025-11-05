<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

include '../../includes/header.php';

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

// Group purchases by purchase_id
$purchases_grouped = [];
foreach ($purchases as $row) {
    $purchase_id = $row['purchase_id'];
    if (!isset($purchases_grouped[$purchase_id])) {
        $purchases_grouped[$purchase_id] = [
            'purchase_date' => $row['purchase_date'],
            'supplier_name' => $row['supplier_name'],
            'invoice_number' => $row['invoice_number'] ?? '-',
            'total_cost' => $row['total_cost'],
            'payment_method' => $row['payment_method'],
            'items' => []
        ];
    }
    $purchases_grouped[$purchase_id]['items'][] = $row;
}
?>

<main class="main-content">
    <div class="card mb-sm">
        <h2>Purchases Report</h2>
    </div>

    <?php foreach ($purchases_grouped as $purchase_id => $purchase): ?>
    <div class="card mb-md">
        <h3>Purchase #<?= $purchase_id ?> | <?= htmlspecialchars($purchase['purchase_date']) ?> | Supplier: <?= htmlspecialchars($purchase['supplier_name']) ?></h3>
        <p>Invoice: <?= htmlspecialchars($purchase['invoice_number']) ?> | Payment: <?= htmlspecialchars($purchase['payment_method']) ?></p>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Cost per Unit</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($purchase['items'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= (int)$item['quantity'] ?></td>
                    <td><?= CURRENCY ?> <?= number_format($item['cost_per_unit'], 2) ?></td>
                    <td><?= CURRENCY ?> <?= number_format($item['subtotal'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><strong>Total Cost:</strong> <?= CURRENCY ?> <?= number_format($purchase['total_cost'], 2) ?></p>
    </div>
    <?php endforeach; ?>
</main>

<?php include '../../includes/footer.php'; ?>
