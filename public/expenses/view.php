<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM expenses WHERE expense_id = ?");
$stmt->execute([$id]);
$expense = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$expense) die("Expense not found.");

include '../../includes/header.php';
?>

<main class="main-content">

    <!-- Page Header -->
    <div class="card mb-sm">
        <h2>Expense Details ID No: <?= htmlspecialchars($expense['expense_id']) ?></h2>
    </div>

    <!-- Expense Details Table -->
    <div class="card">
        <table class="table">
            <tbody>
                <tr>
                    <th>Category</th>
                    <td><?= htmlspecialchars($expense['category']) ?></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td><?= $expense['description'] ? nl2br(htmlspecialchars($expense['description'])) : '<em>No description</em>' ?></td>
                </tr>
                <tr>
                    <th>Amount (TZS)</th>
                    <td><strong><?= number_format($expense['amount'], 2) ?></strong></td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td><?= htmlspecialchars($expense['expense_date']) ?></td>
                </tr>
                <tr>
                    <th>Payment Method</th>
                    <td><?= htmlspecialchars(ucwords(str_replace('_', ' ', $expense['payment_method']))) ?></td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td><?= htmlspecialchars($expense['created_at'] ?? '—') ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Action Buttons -->
        <div class="flex gap-md mt-sm">
            <a href="edit.php?id=<?= $id ?>" class="btn btn-primary">Edit</a>
            <a href="delete.php?id=<?= $id ?>" class="btn btn-danger"
               onclick="return confirm('Are you sure you want to delete this expense record?');">
               Delete
            </a>
            <a href="index.php" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
