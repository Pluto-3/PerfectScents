<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';
require_login();

$purchase_id = $_GET['id'] ?? null;
if (!$purchase_id) {
    die("Purchase ID is required.");
}

// Fetch purchase
$stmt = $pdo->prepare("
    SELECT p.*, s.name AS supplier_name
    FROM purchases p
    JOIN suppliers s ON p.supplier_id = s.supplier_id
    WHERE p.purchase_id = :id
");
$stmt->execute([':id' => $purchase_id]);
$purchase = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$purchase) {
    die("Purchase not found.");
}

// Fetch purchase items
$stmtItems = $pdo->prepare("
    SELECT pi.*, pr.name AS product_name
    FROM purchase_items pi
    JOIN products pr ON pi.product_id = pr.product_id
    WHERE pi.purchase_id = :id
");
$stmtItems->execute([':id' => $purchase_id]);
$items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
?>

<h2>Purchase #<?= (int)$purchase['purchase_id'] ?></h2>

<p><strong>Supplier:</strong> <?= htmlspecialchars($purchase['supplier_name']) ?></p>
<p><strong>Date:</strong> <?= htmlspecialchars($purchase['purchase_date']) ?></p>
<p><strong>Total Cost:</strong> <?= number_format($purchase['total_cost'], 2) ?></p>

<h3>Items</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>Product</th>
        <th>Quantity</th>
        <th>Unit Cost</th>
        <th>Subtotal</th>
    </tr>
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['product_name'] ?? '') ?></td>
            <td><?= (int)$item['quantity'] ?></td>
            <td><?= number_format($item['cost_per_unit'], 2) ?></td>
            <td><?= number_format($item['quantity'] * $item['cost_per_unit'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<p>
    <a href="edit.php?id=<?= (int)$purchase['purchase_id'] ?>">Edit</a> |
    <a href="delete.php?id=<?= (int)$purchase['purchase_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
</p>

<?php include '../../includes/footer.php'; ?>
