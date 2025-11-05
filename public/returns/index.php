<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$returns = get_all_returns($pdo);
?>

<?php include '../../includes/header.php'; ?>

<main class="main-content">

    <div class="card mb-sm" style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Returns and Refunds</h2>
    </div>

    <div class="card mb-md flex end">
        <a href="add.php" class="btn btn-primary">Add Return</a>
    </div>

    <div class="card">
        <?php if (empty($returns)): ?>
            <p class="empty-state">No returns found.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sale ID</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Reason</th>
                        <th>Return Date</th>
                        <th class="center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($returns as $r): ?>
                        <tr>
                            <td><?= (int)$r['return_id'] ?></td>
                            <td><?= (int)$r['sale_id'] ?></td>
                            <td><?= htmlspecialchars($r['product_name']) ?></td>
                            <td><?= (int)$r['quantity'] ?></td>
                            <td><?= htmlspecialchars($r['reason']) ?></td>
                            <td><?= htmlspecialchars($r['return_date']) ?></td>
                            <td class="flex center gap-md">
                                <a href="view.php?id=<?= $r['return_id'] ?>" class="btn btn-secondary">View</a>
                                <a href="edit.php?id=<?= $r['return_id'] ?>" class="btn btn-primary">Edit</a>
                                <a href="delete.php?id=<?= $r['return_id'] ?>" class="btn btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
