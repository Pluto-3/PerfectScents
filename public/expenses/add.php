<?php
require_once '../../includes/header.php';
require_once '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $amount = floatval($_POST['amount']);
    $expense_date = $_POST['expense_date'];
    $payment_method = $_POST['payment_method'];

    if ($category && $amount > 0 && $expense_date) {
        $stmt = $pdo->prepare("INSERT INTO expenses (category, description, amount, expense_date, payment_method) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$category, $description, $amount, $expense_date, $payment_method]);
        header('Location: index.php');
        exit;
    } else {
        $error = "Please fill in all required fields correctly.";
    }
}
?>

<h2>Add Expense</h2>

<?php if (!empty($error)): ?>
<p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
    <label>Category:</label>
    <input type="text" name="category" required>

    <label>Description:</label>
    <textarea name="description"></textarea>

    <label>Amount (TZS):</label>
    <input type="number" name="amount" step="0.01" required>

    <label>Date:</label>
    <input type="date" name="expense_date" required>

    <label>Payment Method:</label>
    <select name="payment_method">
        <option value="cash">Cash</option>
        <option value="bank">Bank</option>
        <option value="mobile_money">Mobile Money</option>
    </select>

    <button type="submit">Save Expense</button>
</form>

<?php require_once '../../includes/footer.php'; ?>
