<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$errors = [];

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
        if (add_expense($pdo, $category, $description, $amount, $expense_date, $payment_method)) {
            header('Location: index.php');
            exit;
        } else {
            $errors[] = "Failed to save expense.";
        }
    }
}

include '../../includes/header.php';
?>

<main class="main-content">

    <!-- Header -->
    <div class="card mb-sm">
        <h2>Add New Expense</h2>
    </div>

    <!-- Error Messages -->
    <?php if ($errors): ?>
        <div class="card error mb-md">
            <ul style="color:red;">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Expense Form -->
    <div class="card">
        <form method="POST" class="form">
            <div class="form-group">
                <label>Category:</label>
                <input type="text" name="category" value="<?= htmlspecialchars($_POST['category'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Description:</label>
                <textarea name="description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Amount (TZS):</label>
                <input type="number" name="amount" step="0.01" value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Date:</label>
                <input type="date" name="expense_date" value="<?= htmlspecialchars($_POST['expense_date'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Payment Method:</label>
                <select name="payment_method" required>
                    <option value="">-- Select --</option>
                    <option value="cash" <?= (($_POST['payment_method'] ?? '') === 'cash') ? 'selected' : '' ?>>Cash</option>
                    <option value="bank" <?= (($_POST['payment_method'] ?? '') === 'bank') ? 'selected' : '' ?>>Bank</option>
                    <option value="mobile_money" <?= (($_POST['payment_method'] ?? '') === 'mobile_money') ? 'selected' : '' ?>>Mobile Money</option>
                </select>
            </div>

            <div class="flex gap-md mt-md">
                <button type="submit" class="btn btn-primary">Save Expense</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>
