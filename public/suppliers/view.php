<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

if (!isset($_GET['id'])) die("Supplier ID not specified.");

$supplier_id = (int)$_GET['id'];
$supplier = get_supplier_by_id($pdo, $supplier_id);

if (!$supplier) die("Supplier not found.");
?>

<?php include '../../includes/header.php'; ?>

<main class="main-content">

    <!-- Supplier Info Card -->
    <div class="card mb-sm">
        <h2>Supplier Details - #<?= $supplier_id ?></h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($supplier['name']) ?></p>
        <p><strong>Contact Person:</strong> <?= htmlspecialchars($supplier['contact_person'] ?? 'N/A') ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($supplier['phone'] ?? 'N/A') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($supplier['email'] ?? 'N/A') ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($supplier['address'] ?? 'N/A') ?></p>
        <p><strong>Reliability Score:</strong> <?= number_format($supplier['reliability_score'], 2) ?>/10</p>
    </div>

    <!-- Action Buttons -->
    <div class="card">
        <a href="edit.php?id=<?= $supplier_id ?>" class="btn btn-primary">Edit</a>
        <a href="delete.php?id=<?= $supplier_id ?>" class="btn btn-danger"
           onclick="return confirm('Delete this supplier?')">Delete</a>
        <a href="index.php" class="btn btn-secondary">Back to Suppliers</a>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
