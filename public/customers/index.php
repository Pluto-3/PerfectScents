<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$customers = get_all_customers($pdo);

?>

<?php include '../../includes/header.php'; ?>

<h2>Customers</h2>
<a href="add.php">Add New Customer</a>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Region</th>
        <th>Source</th>
        <th>Created At</th>
        <th>Actions</th>
    </tr>
    <?php foreach($customers as $c): ?>
    <tr>
        <td><?= (int)$c['customer_id'] ?></td>
        <td><?= htmlspecialchars($c['name']) ?></td>
        <td><?= htmlspecialchars($c['phone']) ?></td>
        <td><?= htmlspecialchars($c['email']) ?></td>
        <td><?= htmlspecialchars($c['region']) ?></td>
        <td><?= htmlspecialchars($c['source']) ?></td>
        <td><?= htmlspecialchars($c['created_at']) ?></td>
        <td>
            <a href="view.php?id=<?= (int)$c['customer_id'] ?>">View</a> |
            <a href="edit.php?id=<?= (int)$c['customer_id'] ?>">Edit</a> |
            <a href="delete.php?id=<?= (int)$c['customer_id'] ?>" onclick="return confirm('Delete this customer?');">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include '../../includes/footer.php'; ?>
