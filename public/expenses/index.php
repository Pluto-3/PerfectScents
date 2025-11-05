<?php
require_once '../../includes/header.php';
require_once '../../includes/functions.php';

$stmt = $pdo->query("SELECT * FROM expenses ORDER BY expense_date DESC");
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Expenses</h2>
<a href="add.php" class="btn"> Add Expense</a>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Category</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Payment Method</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($expenses as $expense): ?>
        <tr>
            <td><?= $expense['expense_id'] ?></td>
            <td><?= htmlspecialchars($expense['category']) ?></td>
            <td><?= htmlspecialchars($expense['description']) ?></td>
            <td><?= number_format($expense['amount'], 2) ?></td>
            <td><?= htmlspecialchars($expense['expense_date']) ?></td>
            <td><?= htmlspecialchars($expense['payment_method']) ?></td>
            <td>
                <a href="edit.php?id=<?= $expense['expense_id'] ?>">Edit</a> |
                <a href="view.php?id=<?= $expense['expense_id'] ?>">View (under construction)</a> |
                <a href="delete.php?id=<?= $expense['expense_id'] ?>" 
                   onclick="return confirm('Delete this expense?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once '../../includes/footer.php'; ?>
