<?php
require_once '../../includes/session.php';
require_login();
require_once '../../includes/functions.php';

$id = $_GET['id'] ?? null;
if (!$id) die("Supplier ID missing.");

$supplier = get_supplier($pdo, $id);
if (!$supplier) die("Supplier not found.");
?>

<?php include '../../includes/header.php'; ?>

<h2>Supplier Details</h2>
<ul>
    <li><strong>ID:</strong> <?= $supplier['supplier_id'] ?></li>
    <li><strong>Name:</strong> <?= htmlspecialchars($supplier['name']) ?></li>
    <li><strong>Contact Person:</strong> <?= htmlspecialchars($supplier['contact_person']) ?></li>
    <li><strong>Phone:</strong> <?= htmlspecialchars($supplier['phone']) ?></li>
    <li><strong>Email:</strong> <?= htmlspecialchars($supplier['email']) ?></li>
    <li><strong>Address:</strong> <?= htmlspecialchars($supplier['address']) ?></li>
    <li><strong>Notes:</strong> <?= htmlspecialchars($supplier['notes']) ?></li>
</ul>

<a href="edit.php?id=<?= $supplier['supplier_id'] ?>">Edit Supplier</a>

<?php include '../../includes/footer.php'; ?>
