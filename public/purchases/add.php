<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';
require_login();

// Fetch suppliers and products
$suppliers = get_all_suppliers($pdo);
$products = get_all_products($pdo);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'] ?? null;
    $payment_method = $_POST['payment_method'] ?? null;
    $invoice_number = $_POST['invoice_number'] ?? null;
    $items = $_POST['items'] ?? [];

    // Validate supplier
    if (!$supplier_id) $errors[] = "Supplier is required.";

    // Validate payment method
    $valid_methods = ['cash','bank','credit'];
    if (!$payment_method || !in_array($payment_method, $valid_methods)) {
        $errors[] = "Valid payment method is required.";
    }

    // Validate items
    $valid_items = [];
    foreach ($items as $i) {
        if (!empty($i['product_id']) && !empty($i['quantity']) && !empty($i['cost_per_unit'])) {
            $valid_items[] = [
                'product_id' => (int)$i['product_id'],
                'quantity' => (int)$i['quantity'],
                'cost_per_unit' => (float)$i['cost_per_unit']
            ];
        }
    }
    if (empty($valid_items)) $errors[] = "At least one product with quantity and cost is required.";

    if (empty($errors)) {
        $purchase_id = create_purchase($pdo, $supplier_id, $valid_items, $payment_method, $invoice_number);
        header("Location: view.php?id={$purchase_id}");
        exit;
    }
}

include '../../includes/header.php';
?>

<h2>Add Purchase</h2>

<?php if ($errors): ?>
    <ul style="color:red;">
        <?php foreach ($errors as $err) echo "<li>".htmlspecialchars($err)."</li>"; ?>
    </ul>
<?php endif; ?>

<form method="post">
    <label>Supplier:
        <select name="supplier_id" required>
            <option value="">--Select Supplier--</option>
            <?php foreach ($suppliers as $s): ?>
                <option value="<?= $s['supplier_id'] ?>" <?= ($_POST['supplier_id'] ?? '') == $s['supplier_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Invoice Number:
        <input type="text" name="invoice_number" value="<?= htmlspecialchars($_POST['invoice_number'] ?? '') ?>">
    </label>

    <label>Payment Method:
        <select name="payment_method" required>
            <option value="">--Select Payment Method--</option>
            <?php foreach (['cash','bank','credit'] as $m): ?>
                <option value="<?= $m ?>" <?= ($_POST['payment_method'] ?? '') == $m ? 'selected' : '' ?>>
                    <?= ucfirst($m) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <h3>Products</h3>
    <table id="products-table" border="1" cellpadding="5">
        <tr><th>Product</th><th>Quantity</th><th>Unit Cost</th><th>Action</th></tr>
        <tr>
            <td>
                <select name="items[0][product_id]" required>
                    <option value="">--Select Product--</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><input type="number" name="items[0][quantity]" min="1" required></td>
            <td><input type="number" name="items[0][cost_per_unit]" min="0" step="0.01" required></td>
            <td><button type="button" onclick="removeRow(this)">Remove</button></td>
        </tr>
    </table>
    <button type="button" onclick="addRow()">Add Product</button>
    <br><br>
    <button type="submit">Save Purchase</button>
</form>

<script>
let rowIndex = 1;
function addRow() {
    const table = document.getElementById('products-table');
    const row = table.insertRow();
    row.innerHTML = `
        <td>
            <select name="items[${rowIndex}][product_id]" required>
                <option value="">--Select Product--</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><input type="number" name="items[${rowIndex}][quantity]" min="1" required></td>
        <td><input type="number" name="items[${rowIndex}][cost_per_unit]" min="0" step="0.01" required></td>
        <td><button type="button" onclick="removeRow(this)">Remove</button></td>
    `;
    rowIndex++;
}

function removeRow(btn) {
    const row = btn.closest('tr');
    row.remove();
}
</script>

<?php include '../../includes/footer.php'; ?>
