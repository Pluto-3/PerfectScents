<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$products = get_all_products($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $stock_in = $_POST['stock_in'] ?? 0;
    $stock_out = $_POST['stock_out'] ?? 0;

    $current_stock = get_current_stock($pdo, $product_id);
    if (!$current_stock) $current_stock = 0;

    $new_stock = $current_stock + $stock_in - $stock_out;
    if ($new_stock < 0) die("Error: Cannot reduce below zero stock.");

    $data = [
        'product_id' => $product_id,
        'stock_in' => $stock_in,
        'stock_out' => $stock_out,
        'current_stock' => $new_stock
    ];

    if (add_inventory_entry($pdo, $data)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Failed to record inventory.";
    }
}

include '../../includes/header.php';
?>

<h2>Add Inventory Adjustment</h2>

<form method="POST">
    <label>Product:</label><br>
    <select name="product_id" required>
        <option value="">--Select Product--</option>
        <?php foreach ($products as $p): ?>
            <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
        <?php endforeach; ?>
    </select><br>

    <label>Stock In:</label><br>
    <input type="number" name="stock_in" min="0" value="0"><br>

    <label>Stock Out:</label><br>
    <input type="number" name="stock_out" min="0" value="0"><br>

    <button type="submit">Save</button>
</form>

<?php include '../../includes/footer.php'; ?>
