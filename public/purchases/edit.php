<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$purchase_id = $_GET['id'] ?? null;
if (!$purchase_id) die("Purchase ID is required.");

// Fetch suppliers and products
$suppliers = get_all_suppliers($pdo);
$products = get_all_products($pdo);

// Fetch purchase with items
$purchase = get_purchase_by_id($pdo, $purchase_id);
if (!$purchase) die("Purchase not found.");
$existing_items = $purchase['items'] ?? [];

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'] ?? null;
    $items = $_POST['items'] ?? [];

    if (!$supplier_id) $errors[] = "Supplier is required.";

    $valid_items = [];
    foreach ($items as $i) {
        if (!empty($i['product_id']) && isset($i['quantity']) && isset($i['cost_per_unit'])) {
            $valid_items[] = [
                'product_id' => (int)$i['product_id'],
                'quantity' => (int)$i['quantity'],
                'cost_per_unit' => (float)$i['cost_per_unit']
            ];
        }
    }
    if (empty($valid_items)) $errors[] = "At least one product with quantity and cost is required.";

    if (!$errors) {
        $pdo->beginTransaction();
        try {
            $total_cost = 0;
            foreach ($valid_items as $i) $total_cost += $i['quantity'] * $i['cost_per_unit'];

            $stmtUpdate = $pdo->prepare("UPDATE purchases SET supplier_id = :supplier_id, total_cost = :total_cost WHERE purchase_id = :id");
            $stmtUpdate->execute([
                ':supplier_id' => $supplier_id,
                ':total_cost' => $total_cost,
                ':id' => $purchase_id
            ]);

            $stmtDel = $pdo->prepare("DELETE FROM purchase_items WHERE purchase_id = :id");
            $stmtDel->execute([':id' => $purchase_id]);

            $stmtInsert = $pdo->prepare("INSERT INTO purchase_items (purchase_id, product_id, quantity, cost_per_unit) VALUES (:purchase_id, :product_id, :quantity, :cost_per_unit)");
            foreach ($valid_items as $item) {
                $stmtInsert->execute([
                    ':purchase_id' => $purchase_id,
                    ':product_id' => $item['product_id'],
                    ':quantity' => $item['quantity'],
                    ':cost_per_unit' => $item['cost_per_unit']
                ]);
            }

            $pdo->commit();
            header("Location: view.php?id={$purchase_id}");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = $e->getMessage();
        }
    }
}

include '../../includes/header.php';
?>

<main class="main-content">

    <div class="card mb-sm" style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Edit Purchase #<?= (int)$purchase['purchase_id'] ?></h2>
    </div><br>

    <?php if ($errors): ?>
        <div class="alert alert-error mb-sm">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="post" class="form-grid">

            <!-- Supplier selection -->
            <div class="form-group">
                <label for="supplier">Supplier <span class="text-danger">*</span></label>
                <select name="supplier_id" id="supplier" required>
                    <option value="">--Select Supplier--</option>
                    <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s['supplier_id'] ?>" <?= ($purchase['supplier_id'] ?? '') == $s['supplier_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Products -->
            <h3>Products</h3>
            <div id="products-container">
                <?php foreach ($existing_items as $index => $item): ?>
                    <div class="form-group flex gap-md align-center product-item">
                        <select name="items[<?= $index ?>][product_id]" required>
                            <option value="">--Select Product--</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['product_id'] ?>" <?= $item['product_id'] == $p['product_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" name="items[<?= $index ?>][quantity]" min="1" value="<?= (int)$item['quantity'] ?>" required placeholder="Quantity">
                        <input type="number" name="items[<?= $index ?>][cost_per_unit]" min="0" step="0.01" value="<?= number_format($item['cost_per_unit'], 2) ?>" required placeholder="Unit Cost">
                        <button type="button" class="btn btn-danger remove-product">Remove</button>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($existing_items)): ?>
                    <div class="form-group flex gap-md align-center product-item">
                        <select name="items[0][product_id]" required>
                            <option value="">--Select Product--</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" name="items[0][quantity]" min="1" required placeholder="Quantity">
                        <input type="number" name="items[0][cost_per_unit]" min="0" step="0.01" required placeholder="Unit Cost">
                        <button type="button" class="btn btn-danger remove-product">Remove</button>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-actions mt-sm">
                <button type="button" class="btn btn-secondary" id="add-product-btn">Add Product</button>
            </div>

            <div class="form-actions mt-sm">
                <button type="submit" class="btn btn-primary">Update Purchase</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>

        </form>
    </div>

</main>

<script>
let rowIndex = <?= count($existing_items) ?>;

document.getElementById('add-product-btn').addEventListener('click', () => {
    const container = document.getElementById('products-container');
    const div = document.createElement('div');
    div.className = 'form-group flex gap-md align-center product-item';
    div.innerHTML = `
        <select name="items[${rowIndex}][product_id]" required>
            <option value="">--Select Product--</option>
            <?php foreach ($products as $p): ?>
                <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="items[${rowIndex}][quantity]" min="1" required placeholder="Quantity">
        <input type="number" name="items[${rowIndex}][cost_per_unit]" min="0" step="0.01" required placeholder="Unit Cost">
        <button type="button" class="btn btn-danger remove-product">Remove</button>
    `;
    container.appendChild(div);
    rowIndex++;
});

document.addEventListener('click', e => {
    if (e.target && e.target.classList.contains('remove-product')) {
        e.target.closest('.product-item').remove();
    }
});
</script>
