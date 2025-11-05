<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$purchases = get_all_purchases($pdo);

include '../../includes/header.php';
?>

<main class="main-content">

    <!-- Page Header -->
    <div class="card mb-sm" style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Purchases</h2>
    </div>

    <div class="card mb-md flex end">
        <a href="add.php" class="btn btn-primary">Add New Purchase</a>
    </div>

    <!-- Purchases Table -->
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Supplier</th>
                    <th>Date</th>
                    <th>Total Cost (TZS)</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($purchases): ?>
                    <?php foreach ($purchases as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['purchase_id']) ?></td>
                            <td><?= htmlspecialchars($p['supplier_name']) ?></td>
                            <td><?= htmlspecialchars($p['purchase_date']) ?></td>
                            <td><?= number_format($p['total_cost'], 2) ?></td>
                            <td class="flex center gap-md">
                                <a href="view.php?id=<?= $p['purchase_id'] ?>" class="btn btn-secondary">View</a>
                                <a href="edit.php?id=<?= $p['purchase_id'] ?>" class="btn btn-primary">Edit</a>
                                <a href="delete.php?id=<?= $p['purchase_id'] ?>" class="btn btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this purchase?');">
                                   Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="center text-muted">No purchases found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
