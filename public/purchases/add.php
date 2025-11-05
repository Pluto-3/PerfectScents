<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

// Fetch suppliers and products
$suppliers = get_all_suppliers($pdo);
$products  = get_all_products($pdo);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id    = $_POST['supplier_id'] ?? null;
    $payment_method = $_POST['payment_method'] ?? null;
    $invoice_number = trim($_POST['invoice_number'] ?? '');
    $items          = $_POST['items'] ?? [];

    // Validate supplier
    if (!$supplier_id) {
        $errors[] = "Supplier is required.";
    }

    // Validate payment method
    $valid_methods = ['cash', 'bank', 'credit'];
    if (!$payment_method || !in_array($payment_method, $valid_methods)) {
        $errors[] = "Valid payment method is required.";
    }

    // Validate products
    $valid_items = [];
    foreach ($items as $i) {
        if (!empty($i['product_id']) && !empty($i['quantity']) && !empty($i['cost_per_unit'])) {
            $valid_items[] = [
                'product_id'    => (int)$i['product_id'],
                'quantity'      => (int)$i['quantity'],
                'cost_per_unit' => (float)$i['cost_per_unit']
            ];
        }
    }

    if (empty($valid_items)) {
        $errors[] = "At least one product with valid quantity and cost is required.";
    }

    // Process purchase
    if (empty($errors)) {
        try {
            $purchase_id = create_purchase($pdo, $supplier_id, $valid_items, $payment_method, $invoice_number);
            header("Location: view.php?id={$purchase_id}");
            exit;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}

include '../../includes/header.php';
?>

<div class="page-header">
    <div class="card mb-sm" style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Add Purchase</h2>
    </div>
</div>

<div class="card">
    <div class="card-content">
        <?php if ($errors): ?>
            <ul class="error-list">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="post" class="form-grid">
            <div class="form-group">
                <label>Supplier</label>
                <select name="supplier_id" required>
                    <option value="">-- Select Supplier --</option>
                    <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s['supplier_id'] ?>" <?= ($_POST['supplier_id'] ?? '') == $s['supplier_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Invoice Number</label>
                <input type="text" name="invoice_number" value="<?= htmlspecialchars($_POST['invoice_number'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Payment Method</label>
                <select name="payment_method" required>
                    <option value="">-- Select Payment Method --</option>
                    <?php foreach (['cash','bank','credit'] as $method): ?>
                        <option value="<?= $method ?>" <?= ($_POST['payment_method'] ?? '') === $method ? 'selected' : '' ?>>
                            <?= ucfirst($method) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <h3 class="section-title">Purchase Items</h3>

            <table id="products-table" class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Cost (TZS)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <select name="items[0][product_id]" required>
                                <option value="">-- Select Product --</option>
                                <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="number" name="items[0][quantity]" min="1" required></td>
                        <td><input type="number" name="items[0][cost_per_unit]" min="0" step="0.01" required></td>
                        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">Remove</button></td>
                    </tr>
                </tbody>
            </table><br>

            <div class="form-actions">
                <button type="button" class="btn btn-sm btn-outline" onclick="addRow()">Add Product</button>
            </div><br>

            <div class="form-actions end">
                <button type="submit" class="btn btn-primary">Save Purchase</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php'">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
let rowIndex = 1;

function addRow() {
    const table = document.getElementById('products-table').querySelector('tbody');
    const row = document.createElement('tr');

    row.innerHTML = `
        <td>
            <select name="items[${rowIndex}][product_id]" required>
                <option value="">-- Select Product --</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><input type="number" name="items[${rowIndex}][quantity]" min="1" required></td>
        <td><input type="number" name="items[${rowIndex}][cost_per_unit]" min="0" step="0.01" required></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">Remove</button></td>
    `;

    table.appendChild(row);
    rowIndex++;
}

function removeRow(button) {
    button.closest('tr').remove();
}
</script>

<?php include '../../includes/footer.php'; ?>
