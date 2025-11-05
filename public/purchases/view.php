<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$purchase_id = $_GET['id'] ?? null;
if (!$purchase_id) die("Purchase ID is required.");

// Fetch purchase
$purchase = get_purchase_by_id($pdo, $purchase_id);
if (!$purchase) die("Purchase not found.");

$items = $purchase['items'] ?? [];

include '../../includes/header.php';
?>

<main class="main-content">

    <div class="card mb-sm">
        <h2>Purchase Details #<?= (int)$purchase['purchase_id'] ?></h2>
    </div>

    <!-- Purchase Info Table -->
    <div class="card mb-md">
        <table class="table table-striped table-compact">
            <tbody>
                <tr>
                    <th>Supplier:</th>
                    <td><?= htmlspecialchars($purchase['supplier_name']) ?></td>
                </tr>
                <tr>
                    <th>Date:</th>
                    <td><?= htmlspecialchars($purchase['purchase_date']) ?></td>
                </tr>
                <tr>
                    <th>Invoice Number:</th>
                    <td><?= htmlspecialchars($purchase['invoice_number'] ?? '-') ?></td>
                </tr>
                <tr>
                    <th>Payment Method:</th>
                    <td><?= ucfirst(htmlspecialchars($purchase['payment_method'] ?? '-')) ?></td>
                </tr>
                <tr>
                    <th>Total Cost:</th>
                    <td><?= number_format($purchase['total_cost'], 2) ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Purchased Items -->
    <div class="card">
        <h3>Purchased Items</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Cost</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($items): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name'] ?? '-') ?></td>
                            <td><?= (int)$item['quantity'] ?></td>
                            <td><?= number_format($item['cost_per_unit'], 2) ?></td>
                            <td><?= number_format($item['quantity'] * $item['cost_per_unit'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="center text-muted">No items found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Actions -->
    <div class="card mt-sm flex gap-md">
        <a href="edit.php?id=<?= (int)$purchase['purchase_id'] ?>" class="btn btn-primary">Edit Purchase</a>
        <a href="delete.php?id=<?= (int)$purchase['purchase_id'] ?>" class="btn btn-danger"
           onclick="return confirm('Are you sure you want to delete this purchase?');">Delete Purchase</a>
        <a href="index.php" class="btn btn-secondary">Back to Purchases</a>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
