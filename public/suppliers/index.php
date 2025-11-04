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
        <th>Contact</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Actions</th>
    </tr>
    <?php foreach($suppliers as $s): ?>
    <tr>
        <td><?= $s['supplier_id'] ?></td>
        <td><?= htmlspecialchars($s['name']) ?></td>
        <td><?= htmlspecialchars($s['contact_person']) ?></td>
        <td><?= htmlspecialchars($s['phone']) ?></td>
        <td><?= htmlspecialchars($s['email']) ?></td>
        <td>
            <a href="view.php?id=<?= $s['supplier_id'] ?>">View</a> |
            <a href="edit.php?id=<?= $s['supplier_id'] ?>">Edit</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include '../../includes/footer.php'; ?>
