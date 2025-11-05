<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';
require_login();

$purchase_id = $_GET['id'] ?? null;
if (!$purchase_id) {
    die("Purchase ID is required.");
}

// Fetch suppliers and products
$suppliers = get_all_suppliers($pdo);
$products = get_all_products($pdo); // make sure this function exists

// Fetch existing purchase
$stmt = $pdo->prepare("SELECT * FROM purchases WHERE purchase_id = :id");
$stmt->execute([':id' => $purchase_id]);
$purchase = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$purchase) {
    die("Purchase not found.");
}

// Fetch purchase items
$stmtItems = $pdo->prepare("SELECT * FROM purchase_items WHERE purchase_id = :id");
$stmtItems->execute([':id' => $purchase_id]);
$existing_items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'] ?? null;
    $items = $_POST['items'] ?? [];

    // Validate supplier
    if (!$supplier_id) $errors[] = "Supplier is required.";

    // Validate items
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

    if (empty($errors)) {
        // Update purchase
        $total_cost = 0;
        foreach ($valid_items as $i) {
            $total_cost += $i['quantity'] * $i['cost_per_unit'];
        }

        $stmtUpdate = $pdo->prepare("UPDATE purchases SET supplier_id = :supplier_id, total_cost = :total_cost WHERE purchase_id = :id");
        $stmtUpdate->execute([
            ':supplier_id' => $supplier_id,
            ':total_cost' => $total_cost,
            ':id' => $purchase_id
        ]);

        // Delete existing items
        $stmtDel = $pdo->prepare("DELETE FROM purchase_items WHERE purchase_id = :id");
        $stmtDel->execute([':id' => $purchase_id]);

        // Insert new items
        $stmtInsert = $pdo->prepare("INSERT INTO purchase_items (purchase_id, product_id, quantity, cost_per_unit) VALUES (:purchase_id, :product_id, :quantity, :cost_per_unit)");
        foreach ($valid_items as $item) {
            $stmtInsert->execute([
                ':purchase_id' => $purchase_id,
                ':product_id' => $item['product_id'],
                ':quantity' => $item['quantity'],
                ':cost_per_unit' => $item['cost_per_unit']
            ]);
        }

        header("Location: view.php?id={$purchase_id}");
        exit;
    }
}

include '../../includes/header.php';
?>

<h2>Edit Purchase #<?= (int)$purchase['purchase_id'] ?></h2>

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
                <option value="<?= $s['supplier_id'] ?>" <?= ($purchase['supplier_id'] ?? '') == $s['supplier_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <h3>Products</h3>
    <table id="products-table" border="1" cellpadding="5">
        <tr><th>Product</th><th>Quantity</th><th>Unit Cost</th><th>Action</th></tr>
        <?php foreach ($existing_items as $index => $item): ?>
            <tr>
                <td>
                    <select name="items[<?= $index ?>][product_id]">
                        <option value="">--Select Product--</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['product_id'] ?>" <?= $item['product_id'] == $p['product_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><input type="number" name="items[<?= $index ?>][quantity]" min="1" value="<?= (int)$item['quantity'] ?>"></td>
                <td><input type="number" name="items[<?= $index ?>][cost_per_unit]" min="0" step="0.01" value="<?= number_format($item['cost_per_unit'], 2) ?>"></td>
                <td><button type="button" onclick="removeRow(this)">Remove</button></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($existing_items)): // ensure at least one empty row ?>
            <tr>
                <td>
                    <select name="items[0][product_id]">
                        <option value="">--Select Product--</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><input type="number" name="items[0][quantity]" min="1"></td>
                <td><input type="number" name="items[0][cost_per_unit]" min="0" step="0.01"></td>
                <td><button type="button" onclick="removeRow(this)">Remove</button></td>
            </tr>
        <?php endif; ?>
    </table>
    <button type="button" onclick="addRow()">Add Product</button>
    <br><br>
    <button type="submit">Update Purchase</button>
</form>

<script>
let rowIndex = <?= count($existing_items) ?>;
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
        <td><input type="number" name="items[${rowIndex}][cost_per_unit]" min="0" step="0.01"></td>
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
