<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$inventory = get_all_inventory($pdo);

include '../../includes/header.php';
?>

<main class="main-content">

    <!-- Page Header -->
    <div class="card mb-sm" style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Inventory</h2>
    </div>

    <div class="card mb-md flex end">
        <a href="add.php" class="btn btn-primary">Add Manual Adjustment</a>
    </div>

    <!-- Inventory Table -->
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Stock In</th>
                    <th>Stock Out</th>
                    <th>Current Stock</th>
                    <th>Last Updated</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($inventory): ?>
                    <?php foreach ($inventory as $i): ?>
                        <tr>
                            <td><?= htmlspecialchars($i['inventory_id']) ?></td>
                            <td><?= htmlspecialchars($i['product_name']) ?></td>
                            <td><?= (int)$i['stock_in'] ?></td>
                            <td><?= (int)$i['stock_out'] ?></td>
                            <td><?= (int)$i['current_stock'] ?></td>
                            <td><?= htmlspecialchars($i['last_updated']) ?></td>
                            <td class="flex center gap-md">
                                <a href="view.php?id=<?= $i['inventory_id'] ?>" class="btn btn-secondary">View</a>
                                <a href="edit.php?id=<?= $i['inventory_id'] ?>" class="btn btn-primary">Edit</a>
                                <a href="delete.php?id=<?= $i['inventory_id'] ?>" class="btn btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this inventory record?');">
                                   Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="center text-muted">No inventory records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
