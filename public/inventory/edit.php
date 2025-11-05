<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = $_GET['id'] ?? null;
$inventory = get_inventory_by_id($pdo, $id);
if (!$inventory) die("Inventory record not found.");

$products = get_all_products($pdo);

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $stock_in = (int)($_POST['stock_in'] ?? 0);
    $stock_out = (int)($_POST['stock_out'] ?? 0);

    if (!$product_id) $errors[] = "Product is required.";

    $new_stock = $inventory['current_stock'] + $stock_in - $stock_out;
    if ($new_stock < 0) $errors[] = "Error: Stock cannot go below zero.";

    if (empty($errors)) {
        $data = [
            'product_id' => $product_id,
            'stock_in' => $stock_in,
            'stock_out' => $stock_out,
            'current_stock' => $new_stock
        ];

        if (update_inventory($pdo, $id, $data)) {
            $success = "Inventory updated successfully.";
            $inventory = get_inventory_by_id($pdo, $id); // Refresh data
        } else {
            $errors[] = "Failed to update inventory record.";
        }
    }
}

include '../../includes/header.php';
?>

<main class="main-content">

    <div class="card mb-sm" style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Edit Inventory Record #<?= htmlspecialchars($inventory['inventory_id']) ?></h2>
    </div>

    <?php if ($errors): ?>
        <div class="alert alert-error mb-sm">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success mb-sm"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" class="form-grid">
            <div class="form-group">
                <label for="product_id">Product</label>
                <select name="product_id" id="product_id" required>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['product_id'] ?>" <?= $inventory['product_id'] == $p['product_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="stock_in">Stock In</label>
                <input type="number" name="stock_in" id="stock_in" min="0" value="<?= (int)$inventory['stock_in'] ?>">
            </div>

            <div class="form-group">
                <label for="stock_out">Stock Out</label>
                <input type="number" name="stock_out" id="stock_out" min="0" value="<?= (int)$inventory['stock_out'] ?>">
            </div>

            <div class="form-group">
                <label for="current_stock">Current Stock</label>
                <input type="number" name="current_stock" id="current_stock" readonly value="<?= (int)$inventory['current_stock'] ?>">
            </div>

            <div class="form-actions mt-sm">
                <button type="submit" class="btn btn-primary">Update Inventory</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
