<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$purchase_id = $_GET['id'] ?? null;
if (!$purchase_id) die("Purchase ID is required.");

$purchase = get_purchase_by_id($pdo, $purchase_id);
if (!$purchase) die("Purchase not found.");

$suppliers = get_all_suppliers($pdo);
$products = get_all_products($pdo);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'] ?? null;
    $items = $_POST['items'] ?? [];

    if (!$supplier_id) $errors[] = "Supplier is required.";

    $valid_items = [];
    foreach ($items as $i) {
        if (!empty($i['product_id']) && !empty($i['quantity']) && !empty($i['unit_cost'])) {
            $valid_items[] = [
                'product_id' => (int)$i['product_id'],
                'quantity' => (int)$i['quantity'],
                'unit_cost' => (float)$i['unit_cost']
            ];
        }
    }
    if (empty($valid_items)) $errors[] = "At least one product with quantity and cost is required.";

    if (empty($errors)) {
        // Strict update: reverse old inventory, then apply new
        delete_purchase($pdo, $purchase_id);
        $purchase_id = create_purchase($pdo, $supplier_id, $valid_items, $_SESSION['user_id']);
        header("Location: view.php?id={$purchase_id}");
        exit;
    }
}

include '../../includes/header.php';
?>

<h2>Edit Purchase #<?= $purchase['purchase_id'] ?></h2>

<?php if ($errors): ?>
<ul style="color:red;">
<?php foreach ($errors as $err) echo "<li>{$err}</li>"; ?>
</ul>
<?php endif; ?>

<form method="post">
<label>Supplier:
<select name="supplier_id">
<option value="">--Select Supplier--</option>
<?php foreach ($suppliers as $s): ?>
<option value="<?= $s['supplier_id'] ?>" <?= ($purchase['supplier_id'] == $s['supplier_id']) ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
<?php endforeach; ?>
</select>
</label>

<h3>Products</h3>
<table id="products-table" border="1" cellpadding="5">
<tr><th>Product</th><th>Quantity</th><th>Unit Cost</th><th>Action</th></tr>
<?php foreach ($purchase['items'] as $i => $item): ?>
<tr>
<td>
<select name="items[<?= $i ?>][product_id]">
<option value="">--Select Product--</option>
<?php foreach ($products as $p): ?>
<option value="<?= $p['product_id'] ?>" <?= ($p['product_id'] == $item['product_id']) ? 'selected' : '' ?>><?= htmlspecialchars($p['name']) ?></option>
<?php endforeach; ?>
</select>
</td>
<td><input type="number" name="items[<?= $i ?>][quantity]" min="1" value="<?= $item['quantity'] ?>"></td>
<td><input type="number" name="items[<?= $i ?>][unit_cost]" min="0" step="0.01" value="<?= $item['unit_cost'] ?>"></td>
<td><button type="button" onclick="removeRow(this)">Remove</button></td>
</tr>
<?php endforeach; ?>
</table>
<button type="button" onclick="addRow()">Add Product</button>
<br><br>
<button type="submit">Update Purchase</button>
</form>

<script>
let rowIndex = <?= count($purchase['items']) ?>;
function addRow() {
    const table = document.getElementById('products-table');
    const row = table.insertRow();
    row.innerHTML = `
<td>
<select name="items[${rowIndex}][product_id]">
<option value="">--Select Product--</option>
<?php foreach ($products as $p): ?>
<option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
<?php endforeach; ?>
</select>
</td>
<td><input type="number" name="items[${rowIndex}][quantity]" min="1"></td>
<td><input type="number" name="items[${rowIndex}][unit_cost]" min="0" step="0.01"></td>
<td><button type="button" onclick="removeRow(this)">Remove</button></td>`;
    rowIndex++;
}
function removeRow(btn) { const row = btn.closest('tr'); row.remove(); }
</script>

<?php include '../../includes/footer.php'; ?>
