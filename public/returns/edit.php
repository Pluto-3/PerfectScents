<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$return_id = (int)$_GET['id'];
$return = get_return_by_id($pdo, $return_id);
$errors = [];
$success = '';

if (!$return) die("Return not found.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sale_id = (int)$_POST['sale_id'];
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $reason = trim($_POST['reason']);

    try {
        edit_return($pdo, $return_id, $sale_id, $product_id, $quantity, $reason);
        $success = "Return updated successfully.";
        $return = get_return_by_id($pdo, $return_id); // reload
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}

$sales = $pdo->query("SELECT sale_id FROM sales ORDER BY sale_date DESC")->fetchAll(PDO::FETCH_ASSOC);
$products = $pdo->query("SELECT product_id, name FROM products WHERE status='active'")->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include '../../includes/header.php'; ?>

<h2>Edit Return</h2>

<?php if($errors): ?>
    <ul style="color:red;">
        <?php foreach($errors as $err) echo "<li>$err</li>"; ?>
    </ul>
<?php endif; ?>

<?php if($success): ?>
    <p style="color:green;"><?= $success ?></p>
<?php endif; ?>

<form method="post">
    <label>Sale ID:</label>
    <select name="sale_id" required>
        <option value="">Select Sale</option>
        <?php foreach($sales as $s): ?>
            <option value="<?= $s['sale_id'] ?>" <?= $return['sale_id'] == $s['sale_id'] ? 'selected' : '' ?>><?= $s['sale_id'] ?></option>
        <?php endforeach; ?>
    </select><br>

    <label>Product:</label>
    <select name="product_id" required>
        <option value="">Select Product</option>
        <?php foreach($products as $p): ?>
            <option value="<?= $p['product_id'] ?>" <?= $return['product_id'] == $p['product_id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['name']) ?></option>
        <?php endforeach; ?>
    </select><br>

    <label>Quantity:</label>
    <input type="number" name="quantity" min="1" value="<?= $return['quantity'] ?>" required><br>

    <label>Reason:</label>
    <textarea name="reason" required><?= htmlspecialchars($return['reason']) ?></textarea><br>

    <button type="submit">Update Return</button>
</form>

<?php include '../../includes/footer.php'; ?>
