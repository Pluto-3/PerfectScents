<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$products = get_all_products($pdo);

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $stock_in = (int)($_POST['stock_in'] ?? 0);
    $stock_out = (int)($_POST['stock_out'] ?? 0);

    if (!$product_id) $errors[] = "Product is required.";

    $current_stock = get_current_stock($pdo, $product_id) ?? 0;
    $new_stock = $current_stock + $stock_in - $stock_out;

    if ($new_stock < 0) $errors[] = "Error: Stock cannot go below zero.";

    if (empty($errors)) {
        $data = [
            'product_id' => $product_id,
            'stock_in' => $stock_in,
            'stock_out' => $stock_out,
            'current_stock' => $new_stock
        ];

        if (add_inventory_entry($pdo, $data)) {
            $success = "Inventory adjustment added successfully.";
        } else {
            $errors[] = "Failed to record inventory adjustment.";
        }
    }
}

include '../../includes/header.php';
?>

<main class="main-content">

    <div class="card mb-sm" style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Add Inventory Adjustment</h2>
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
                    <option value="">--Select Product--</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="stock_in">Stock In</label>
                <input type="number" name="stock_in" id="stock_in" min="0" value="0">
            </div>

            <div class="form-group">
                <label for="stock_out">Stock Out</label>
                <input type="number" name="stock_out" id="stock_out" min="0" value="0">
            </div>

            <div class="form-actions mt-sm">
                <button type="submit" class="btn btn-primary">Save Adjustment</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
