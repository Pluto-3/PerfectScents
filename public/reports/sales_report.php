<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

include '../../includes/header.php';

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

// Group sales by sale_id for cleaner display
$sales_grouped = [];
foreach ($sales as $row) {
    $sale_id = $row['sale_id'];
    if (!isset($sales_grouped[$sale_id])) {
        $sales_grouped[$sale_id] = [
            'sale_date' => $row['sale_date'],
            'customer_name' => $row['customer_name'] ?? 'Walk-in',
            'total_amount' => $row['total_amount'],
            'discount' => $row['discount'],
            'payment_method' => $row['payment_method'],
            'items' => []
        ];
    }
    $sales_grouped[$sale_id]['items'][] = $row;
}
?>

<main class="main-content">
    <div class="card mb-sm">
        <h2>Sales Report</h2>
    </div>

    <?php foreach ($sales_grouped as $sale_id => $sale): ?>
    <div class="card mb-md">
        <h3>Sale #<?= $sale_id ?> | <?= htmlspecialchars($sale['sale_date']) ?> | <?= htmlspecialchars($sale['customer_name']) ?></h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sale['items'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= (int)$item['quantity'] ?></td>
                    <td><?= CURRENCY ?> <?= number_format($item['unit_price'], 2) ?></td>
                    <td><?= CURRENCY ?> <?= number_format($item['subtotal'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><strong>Total:</strong> <?= CURRENCY ?> <?= number_format($sale['total_amount'], 2) ?> |
           <strong>Discount:</strong> <?= CURRENCY ?> <?= number_format($sale['discount'], 2) ?> |
           <strong>Payment:</strong> <?= htmlspecialchars($sale['payment_method']) ?></p>
    </div>
    <?php endforeach; ?>
</main>

<?php include '../../includes/footer.php'; ?>
