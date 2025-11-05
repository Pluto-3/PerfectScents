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
        <th>Brand</th>
        <th>Category</th>
        <th>Size (ml)</th>
        <th>Cost Price</th>
        <th>Retail Price</th>
        <th>Supplier name</th>
        <th>Status</th>
        <th>Description</th>
        <th>Actions</th>
    </tr>
    <?php foreach($products as $p): ?>
    <tr>
        <td><?= $p['product_id'] ?></td>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= htmlspecialchars($p['brand']) ?></td>
        <td><?= htmlspecialchars($p['category']) ?></td>
        <td><?= $p['size_ml'] ?></td>
        <td><?= number_format($p['cost_price'], 2) ?></td>
        <td><?= number_format($p['retail_price'], 2) ?></td>
        <td><?= htmlspecialchars($p['supplier_name'] ?: '-') ?></td>
        <td><?= htmlspecialchars($p['status']) ?></td>
        <td><?= htmlspecialchars($p['description']) ?></td>
        <td>
            <a href="edit.php?id=<?= $p['product_id'] ?>">Edit</a> |
            <a href="view.php?id=<?= $p['product_id'] ?>">View</a> |
            <a href="delete.php?id=<?= $p['product_id'] ?>" onclick="return confirm('Delete this product?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include '../../includes/footer.php'; ?>
