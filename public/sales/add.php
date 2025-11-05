<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

// Fetch customers and products
$customers = get_all_customers($pdo); // make sure this function exists
$products = get_all_products($pdo);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'] ?: null; // null for walk-in
    $payment_method = $_POST['payment_method'] ?? '';
    $sales_channel = $_POST['sales_channel'] ?? '';
    $discount = (float)($_POST['discount'] ?? 0);
    $items = $_POST['items'] ?? [];

    // Validation
    if (empty($items)) {
        $error = "Please add at least one product to the sale.";
    } else {
        foreach ($items as $index => $item) {
            $qty = (int)($item['quantity'] ?? 0);
            $price = (float)($item['unit_price'] ?? -1);
            $prod_id = (int)($item['product_id'] ?? 0);

            if ($prod_id <= 0) $error = "Invalid product selected at row " . ($index + 1);
            if ($qty <= 0) $error = "Quantity must be at least 1 at row " . ($index + 1);
            if ($price < 0) $error = "Unit price must be >= 0 at row " . ($index + 1);
            if ($error) break;
        }
    }

    if (!$error) {
        try {
            create_sale($pdo, $customer_id, $items, $payment_method, $sales_channel, $discount);
            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>

<?php include '../../includes/header.php'; ?>

<?php if ($error): ?>
<div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<main class="main-content">
    <div class="card">
        <h2 class="card-title">Add Sale</h2>

        <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" id="sale-form" class="form-grid">
            <div class="form-group">
                <label>Customer</label>
                <select name="customer_id">
                    <option value="">Walk-in</option>
                    <?php foreach($customers as $c): ?>
                        <option value="<?= $c['customer_id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Payment Method</label>
                <select name="payment_method" required>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="mobile_money">Mobile Money</option>
                    <option value="bank">Bank</option>
                </select>
            </div>

            <div class="form-group">
                <label>Sales Channel</label>
                <select name="sales_channel" required>
                    <option value="store">Store</option>
                    <option value="online">Online</option>
                    <option value="whatsapp">WhatsApp</option>
                </select>
            </div>

            <div class="form-group mb-sm">
                <label>Discount</label>
                <input type="number" name="discount" value="0" min="0" step="0.01">
            </div>

            <h3>Items</h3>
            <table id="sale-items-table" class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <button type="button" class="btn btn-secondary" onclick="addRow()">Add Product</button>
            <button type="submit" class="btn btn-primary">Save Sale</button>
        </form>
    </div>
</main>

<script>
let rowIndex = 0;
const products = <?= json_encode($products); ?>;

function addRow(product_id = null, quantity = 1, unit_price = 0) {
    const table = document.getElementById('sale-items-table');
    const row = table.insertRow();
    row.innerHTML = `
<td>
<select name="items[${rowIndex}][product_id]" required>
${products.map(p=>`<option value="${p.product_id}" ${product_id==p.product_id?'selected':''}>${p.name}</option>`).join('')}
</select>
</td>
<td><input type="number" name="items[${rowIndex}][quantity]" value="${quantity}" min="1" required></td>
<td><input type="number" name="items[${rowIndex}][unit_price]" value="${unit_price}" min="0" step="0.01" required></td>
<td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Remove</button></td>
`;
    rowIndex++;
}

// Add one default row on page load
addRow();

function removeRow(btn) { 
    btn.closest('tr').remove(); 
}
</script>

<?php include '../../includes/footer.php'; ?>
