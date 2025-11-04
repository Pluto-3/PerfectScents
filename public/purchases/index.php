<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$purchases = get_all_purchases($pdo);
include '../../includes/header.php';
?>

<h2>Purchases</h2>
<a href="add.php">Add New Purchase</a>
<table border="1" cellpadding="5">
<tr>
<th>ID</th><th>Supplier</th><th>Date</th><th>Total Cost</th><th>Actions</th>
</tr>
<?php foreach ($purchases as $p): ?>
<tr>
<td><?= $p['purchase_id'] ?></td>
<td><?= htmlspecialchars($p['supplier_name']) ?></td>
<td><?= $p['purchase_date'] ?></td>
<td><?= number_format($p['total_cost']) ?></td>
<td>
<a href="view.php?id=<?= $p['purchase_id'] ?>">View</a> |
<a href="edit.php?id=<?= $p['purchase_id'] ?>">Edit</a> |
<a href="delete.php?id=<?= $p['purchase_id'] ?>" onclick="return confirm('Delete this purchase?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</table>
<?php include '../../includes/footer.php'; ?>
