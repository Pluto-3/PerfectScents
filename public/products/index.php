<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$products = get_all_products($pdo);
?>

<?php include '../../includes/header.php'; ?>

<h2>Products</h2>
<a href="add.php">Add New Product</a>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Supplier</th>
        <th>Cost Price</th>
        <th>Unit Price</th>
        <th>Actions</th>
    </tr>
    <?php foreach($products as $p): ?>
    <tr>
        <td><?= $p['product_id'] ?></td>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= $p['supplier_id'] ?></td>
        <td><?= CURRENCY . ' ' . number_format($p['cost_price'],2) ?></td>
        <td><?= CURRENCY . ' ' . number_format($p['unit_price'],2) ?></td>
        <td>
            <a href="view.php?id=<?= $p['product_id'] ?>">View</a> |
            <a href="edit.php?id=<?= $p['product_id'] ?>">Edit</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include '../../includes/footer.php'; ?>
