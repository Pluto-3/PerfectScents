<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$inventory = get_all_inventory($pdo);
include '../../includes/header.php';
?>

<h2>Inventory Overview</h2>
<a href="add.php">Add Manual Adjustment</a>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Product</th>
        <th>Stock In</th>
        <th>Stock Out</th>
        <th>Current Stock</th>
        <th>Last Updated</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($inventory as $i): ?>
    <tr>
        <td><?= $i['inventory_id'] ?></td>
        <td><?= htmlspecialchars($i['product_name']) ?></td>
        <td><?= $i['stock_in'] ?></td>
        <td><?= $i['stock_out'] ?></td>
        <td><?= $i['current_stock'] ?></td>
        <td><?= $i['last_updated'] ?></td>
        <td>
            <a href="view.php?id=<?= $i['inventory_id'] ?>">View</a> |
            <a href="edit.php?id=<?= $i['inventory_id'] ?>">Edit</a> |
            <a href="delete.php?id=<?= $i['inventory_id'] ?>" onclick="return confirm('Delete this record?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include '../../includes/footer.php'; ?>
