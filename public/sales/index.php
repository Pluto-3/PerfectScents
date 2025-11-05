<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$sales = get_all_sales($pdo);
?>

<?php include '../../includes/header.php'; ?>

<main class="main-content">

    <!-- Page Header -->
    <div class="card mb-sm" style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Sales</h2>
    </div>

    <div class="card mb-md flex end">
        <a href="add.php" class="btn btn-primary">Add New Sale</a>
    </div>

    <!-- Sales Table -->
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    <th>Sales Channel</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($sales): ?>
                    <?php foreach($sales as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['sale_id']) ?></td>
                            <td><?= htmlspecialchars($s['sale_date']) ?></td>
                            <td><?= htmlspecialchars($s['customer_name'] ?? 'Walk-in') ?></td>
                            <td><?= number_format($s['total_amount'] - $s['discount'], 2) ?></td>
                            <td><?= htmlspecialchars($s['payment_method']) ?></td>
                            <td><?= htmlspecialchars($s['sales_channel']) ?></td>
                            <td class="flex center gap-md">
                                <a href="view.php?id=<?= $s['sale_id'] ?>" class="btn btn-secondary">View</a>
                                <a href="edit.php?id=<?= $s['sale_id'] ?>" class="btn btn-primary">Edit</a>
                                <a href="delete.php?id=<?= $s['sale_id'] ?>" class="btn btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="center text-muted">No sales found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
