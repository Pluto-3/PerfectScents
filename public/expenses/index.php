<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$expenses = get_all_expenses($pdo);

include '../../includes/header.php';
?>

<main class="main-content">

    <!-- Page Header -->
    <div class="card mb-sm" style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Expenses</h2>
    </div>

    <div class="card mb-md flex end">
        <a href="add.php" class="btn btn-primary">Add Expense</a>
    </div>

    <!-- Expenses Table -->
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Amount (TZS)</th>
                    <th>Date</th>
                    <th>Payment Method</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($expenses): ?>
                    <?php foreach ($expenses as $e): ?>
                        <tr>
                            <td><?= htmlspecialchars($e['expense_id']) ?></td>
                            <td><?= htmlspecialchars($e['category']) ?></td>
                            <td><?= htmlspecialchars($e['description']) ?></td>
                            <td><?= number_format($e['amount'], 2) ?></td>
                            <td><?= htmlspecialchars($e['expense_date']) ?></td>
                            <td><?= htmlspecialchars($e['payment_method']) ?></td>
                            <td class="flex center gap-md">
                                <a href="edit.php?id=<?= $e['expense_id'] ?>" class="btn btn-primary">Edit</a>
                                <a href="view.php?id=<?= $e['expense_id'] ?>" class="btn btn-secondary">View</a>
                                <a href="delete.php?id=<?= $e['expense_id'] ?>" class="btn btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this expense?');">
                                   Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="center text-muted">No expenses found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
