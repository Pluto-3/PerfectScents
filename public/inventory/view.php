<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = $_GET['id'] ?? null;
$inventory = get_inventory_by_id($pdo, $id);
if (!$inventory) die("Inventory record not found.");

include '../../includes/header.php';
?>

<main class="main-content">

    <!-- Page Header -->
    <div class="card mb-sm" style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Inventory Record #<?= htmlspecialchars($inventory['inventory_id']) ?></h2>
        <div class="flex gap-md">
            <a href="edit.php?id=<?= $inventory['inventory_id'] ?>" class="btn btn-primary">Edit</a>
            <a href="delete.php?id=<?= $inventory['inventory_id'] ?>" class="btn btn-danger">Delete</a>
            <a href="index.php" class="btn btn-secondary">Back to Inventory</a>
        </div>
    </div>

    <!-- Details Table -->
    <div class="card">
        <table class="table">
            <tbody>
                <tr>
                    <th>Product</th>
                    <td><?= htmlspecialchars($inventory['product_name']) ?></td>
                </tr>
                <tr>
                    <th>Stock In</th>
                    <td><?= (int)$inventory['stock_in'] ?></td>
                </tr>
                <tr>
                    <th>Stock Out</th>
                    <td><?= (int)$inventory['stock_out'] ?></td>
                </tr>
                <tr>
                    <th>Current Stock</th>
                    <td><?= (int)$inventory['current_stock'] ?></td>
                </tr>
                <tr>
                    <th>Last Updated</th>
                    <td><?= htmlspecialchars($inventory['last_updated']) ?></td>
                </tr>
            </tbody>
        </table>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
