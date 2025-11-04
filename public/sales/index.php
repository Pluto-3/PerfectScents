<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$sales = get_all_sales($pdo); // Strict function to fetch all sales
?>

<?php include '../../includes/header.php'; ?>

<h2>Sales</h2>
<a href="add.php">Add New Sale</a>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Date</th>
        <th>Customer</th>
        <th>Total Amount</th>
        <th>Payment Method</th>
        <th>Sales Channel</th>
        <th>Actions</th>
    </tr>
    <?php foreach($sales as $s): ?>
    <tr>
        <td><?= $s['sale_id'] ?></td>
        <td><?= $s['sale_date'] ?></td>
        <td><?= htmlspecialchars($s['customer_name'] ?? 'Walk-in') ?></td>
        <td><?= number_format($s['total_amount'], 2) ?></td>
        <td><?= $s['payment_method'] ?></td>
        <td><?= $s['sales_channel'] ?></td>
        <td>
            <a href="view.php?id=<?= $s['sale_id'] ?>">View</a> |
            <a href="edit.php?id=<?= $s['sale_id'] ?>">Edit</a> |
            <a href="delete.php?id=<?= $s['sale_id'] ?>" onclick="return confirm('Delete this sale?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include '../../includes/footer.php'; ?>
