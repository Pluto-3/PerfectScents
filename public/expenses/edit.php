<?php
require_once '../../includes/header.php';
require_once '../../includes/functions.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM expenses WHERE expense_id = ?");
$stmt->execute([$id]);
$expense = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$expense) {
    die("Expense not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $amount = floatval($_POST['amount']);
    $expense_date = $_POST['expense_date'];
    $payment_method = $_POST['payment_method'];

    if ($category && $amount > 0 && $expense_date) {
        $stmt = $pdo->prepare("UPDATE expenses 
                               SET category=?, description=?, amount=?, expense_date=?, payment_method=? 
                               WHERE expense_id=?");
        $stmt->execute([$category, $description, $amount, $expense_date, $payment_method, $id]);
        header('Location: index.php');
        exit;
    } else {
        $error = "Please fill in all required fields correctly.";
    }
}
?>

<h2>Edit Expense #<?= $id ?></h2>

<?php if (!empty($error)): ?>
<p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
    <label>Category:</label>
    <input type="text" name="category" value="<?= htmlspecialchars($expense['category']) ?>" required>

    <label>Description:</label>
    <textarea name="description"><?= htmlspecialchars($expense['description']) ?></textarea>

    <label>Amount (TZS):</label>
    <input type="number" name="amount" step="0.01" value="<?= htmlspecialchars($expense['amount']) ?>" required>

    <label>Date:</label>
    <input type="date" name="expense_date" value="<?= htmlspecialchars($expense['expense_date']) ?>" required>

    <label>Payment Method:</label>
    <select name="payment_method">
        <option value="cash" <?= $expense['payment_method'] === 'cash' ? 'selected' : '' ?>>Cash</option>
        <option value="bank" <?= $expense['payment_method'] === 'bank' ? 'selected' : '' ?>>Bank</option>
        <option value="mobile_money" <?= $expense['payment_method'] === 'mobile_money' ? 'selected' : '' ?>>Mobile Money</option>
    </select>

    <button type="submit">Update Expense</button>
</form>

<?php require_once '../../includes/footer.php'; ?>
