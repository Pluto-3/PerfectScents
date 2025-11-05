<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = $_GET['id'] ?? null;
$inventory = get_inventory_by_id($pdo, $id);
if (!$inventory) die("Inventory record not found.");

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (delete_inventory_entry($pdo, $id)) {
        $success = "Inventory record deleted successfully.";
    } else {
        $errors[] = "Failed to delete inventory record.";
    }
}

include '../../includes/header.php';
?>

<main class="main-content">

    <div class="card mb-sm">
        <h2>Delete Inventory Record</h2>
        <a href="index.php" class="btn btn-secondary">Back to Inventory</a>
    </div>

    <?php if ($errors): ?>
        <div class="alert alert-error mb-sm">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success mb-sm"><?= htmlspecialchars($success) ?></div>
        <a href="index.php" class="btn btn-primary">Back to Inventory</a>
    <?php else: ?>
        <div class="card">
            <p>Are you sure you want to delete the following inventory record?</p>
            <table class="table">
                <tr><th>Product</th><td><?= htmlspecialchars($inventory['product_name']) ?></td></tr>
                <tr><th>Stock In</th><td><?= $inventory['stock_in'] ?></td></tr>
                <tr><th>Stock Out</th><td><?= $inventory['stock_out'] ?></td></tr>
                <tr><th>Current Stock</th><td><?= $inventory['current_stock'] ?></td></tr>
                <tr><th>Last Updated</th><td><?= htmlspecialchars($inventory['last_updated']) ?></td></tr>
            </table>

            <form method="POST" class="mt-sm">
                <button type="submit" class="btn btn-danger">Confirm Delete</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    <?php endif; ?>

</main>

<?php include '../../includes/footer.php'; ?>
