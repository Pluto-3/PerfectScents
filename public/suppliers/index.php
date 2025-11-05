<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$suppliers = get_all_suppliers($pdo);
?>

<?php include '../../includes/header.php'; ?>

<h2>Suppliers</h2>
<a href="add.php">Add New Supplier</a>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Contact Person</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Address</th>
        <th>Reliability Score</th>
        <th>Actions</th>
    </tr>
    <?php foreach($suppliers as $s): ?>
    <tr>
        <td><?= $s['supplier_id'] ?></td>
        <td><?= htmlspecialchars($s['name']) ?></td>
        <td><?= htmlspecialchars($s['contact_person']) ?></td>
        <td><?= htmlspecialchars($s['phone']) ?></td>
        <td><?= htmlspecialchars($s['email']) ?></td>
        <td><?= htmlspecialchars($s['address']) ?></td>
        <td><?= number_format($s['reliability_score'], 2) ?></td>
        <td>
            <a href="edit.php?id=<?= $s['supplier_id'] ?>">Edit</a> |
            <a href="delete.php?id=<?= $s['supplier_id'] ?>" onclick="return confirm('Delete this supplier?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include '../../includes/footer.php'; ?>
