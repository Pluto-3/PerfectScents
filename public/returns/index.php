<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$returns = get_all_returns($pdo);

?>

<?php include '../../includes/header.php'; ?>

<h2>Returns / Refunds</h2>
<a href="add.php">Add Return</a>
<a href="view.php">View Return</a>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Sale ID</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Reason</th>
        <th>Return Date</th>
        <th>Actions</th>
    </tr>
    <?php foreach($returns as $r): ?>
    <tr>
        <td><?= $r['return_id'] ?></td>
        <td><?= $r['sale_id'] ?></td>
        <td><?= htmlspecialchars($r['product_name']) ?></td>
        <td><?= $r['quantity'] ?></td>
        <td><?= htmlspecialchars($r['reason']) ?></td>
        <td><?= $r['return_date'] ?></td>
        <td>
            <a href="view.php?id=<?= $r['return_id'] ?>">View</a> |
            <a href="edit.php?id=<?= $r['return_id'] ?>">Edit</a> |
            <a href="delete.php?id=<?= $r['return_id'] ?>" onclick="return confirm('Are you sure?');">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include '../../includes/footer.php'; ?>
