<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = (int)($_GET['id'] ?? 0);
$expense = get_expense_by_id($pdo, $id);

if (!$expense) {
    die("Expense not found.");
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $expense_date = $_POST['expense_date'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';

    if (!$category) $errors[] = "Category is required.";
    if ($amount <= 0) $errors[] = "Amount must be greater than zero.";
    if (!$expense_date) $errors[] = "Date is required.";

    if (empty($errors)) {
        if (update_expense($pdo, $id, $category, $description, $amount, $expense_date, $payment_method)) {
            header("Location: view.php?id=$id");
            exit;
        } else {
            $errors[] = "Failed to update expense.";
        }
    }
}

include '../../includes/header.php';
?>

<main class="main-content">

    <!-- Page Header -->
    <div class="card mb-sm" style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Edit Expense #<?= htmlspecialchars($id) ?></h2>
    </div>

    <?php if ($errors): ?>
        <div class="card error mb-md">
            <ul style="color:red;">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Edit Expense Form -->
    <div class="card">
        <form method="POST" class="form">
            <div class="form-group">
                <label>Category:</label>
                <input type="text" name="category" value="<?= htmlspecialchars($expense['category']) ?>" required>
            </div>

            <div class="form-group">
                <label>Description:</label>
                <textarea name="description"><?= htmlspecialchars($expense['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Amount (TZS):</label>
                <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($expense['amount']) ?>" required>
            </div>

            <div class="form-group">
                <label>Date:</label>
                <input type="date" name="expense_date" value="<?= htmlspecialchars($expense['expense_date']) ?>" required>
            </div>

            <div class="form-group">
                <label>Payment Method:</label>
                <select name="payment_method" required>
                    <option value="cash" <?= $expense['payment_method'] === 'cash' ? 'selected' : '' ?>>Cash</option>
                    <option value="bank" <?= $expense['payment_method'] === 'bank' ? 'selected' : '' ?>>Bank</option>
                    <option value="mobile_money" <?= $expense['payment_method'] === 'mobile_money' ? 'selected' : '' ?>>Mobile Money</option>
                </select>
            </div>

            <div class="flex gap-md mt-md">
                <button type="submit" class="btn btn-primary">Update Expense</button>
                <a href="view.php?id=<?= $id ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
