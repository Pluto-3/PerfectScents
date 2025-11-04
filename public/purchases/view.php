<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$purchase_id = $_GET['id'] ?? null;
if (!$purchase_id) die("Purchase ID is required.");

$purchase = get_purchase_by_id($pdo, $purchase_id);
if (!$purchase) die("Purchase not found.");

include '../../includes/header.php';
?>

<h2>Purchase #<?= $purchase['purchase_id'] ?></h2>
<p>Supplier: <?= htmlspecialchars($purchase['supplier_name']) ?></p>
<p>Date: <?= $purchase['purchase_date'] ?></p>
<p>Total Cost: <?= number_format($purchase['total_cost']) ?></p>

<h3>Items</h3>
<table border="1" cellpadding="5">
<tr><th>Product</th><th>Quantity</th><th>Unit Cost</th><th>Subtotal</th></tr>
<?php foreach ($purchase['items'] as $item): ?>
<tr>
<td><?= htmlspecialchars($item['product_name']) ?></td>
<td><?= $item['quantity'] ?></td>
<td><?= number_format($item['unit_cost']) ?></td>
<td><?= number_format($item['subtotal']) ?></td>
</tr>
<?php endforeach; ?>
</table>

<a href="edit.php?id=<?= $purchase['purchase_id'] ?>">Edit</a> |
<a href="delete.php?id=<?= $purchase['purchase_id'] ?>" onclick="return confirm('Delete this purchase?')">Delete</a>

<?php include '../../includes/footer.php'; ?>
