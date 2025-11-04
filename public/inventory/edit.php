<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = $_GET['id'] ?? null;
$inventory = get_inventory_by_id($pdo, $id);
if (!$inventory) die("Record not found");

$products = get_all_products($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $stock_in = $_POST['stock_in'] ?? 0;
    $stock_out = $_POST['stock_out'] ?? 0;
    $current_stock = $_POST['current_stock'];

    $new_stock = $current_stock + $stock_in - $stock_out;
    if ($new_stock < 0) die("Error: Cannot reduce below zero stock.");

    $data = [
        'product_id' => $product_id,
        'stock_in' => $stock_in,
        'stock_out' => $stock_out,
        'current_stock' => $new_stock
    ];

    if (update_inventory($pdo, $id, $data)) {
        header("Location: view.php?id=$id");
        exit;
    } else {
        echo "Failed to update inventory record.";
    }
}

include '../../includes/header.php';
?>

<h2>Edit Inventory Record</h2>
<form method="POST">
    <label>Product:</label><br>
    <select name="product_id" required>
        <?php foreach ($products as $p): ?>
            <option value="<?= $p['product_id'] ?>" <?= $inventory['product_id'] == $p['product_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($p['name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label>Stock In:</label><br>
    <input type="number" name="stock_in" min="0" value="<?= $inventory['stock_in'] ?>"><br>

    <label>Stock Out:</label><br>
    <input type="number" name="stock_out" min="0" value="<?= $inventory['stock_out'] ?>"><br>

    <label>Current Stock:</label><br>
    <input type="number" name="current_stock" readonly value="<?= $inventory['current_stock'] ?>"><br><br>

    <button type="submit">Update</button>
</form>

<?php include '../../includes/footer.php'; ?>
