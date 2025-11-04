<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

if (!isset($_GET['id'])) die("Sale ID not specified.");
$sale_id = (int)$_GET['id'];

// Fetch sale
$sale = get_sale_by_id($pdo, $sale_id);
if (!$sale) die("Sale not found.");

// Fetch all customers and products
$customers = get_all_customers($pdo);
$products = get_all_products($pdo);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'] ?: null;
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
            update_sale($pdo, $sale_id, $customer_id, $items, $payment_method, $sales_channel, $discount);
            header("Location: view.php?id=$sale_id");
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>

<?php include '../../includes/header.php'; ?>

<h2>Edit Sale #<?= $sale_id ?></h2>

<?php if ($error): ?>
<p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" id="sale-form">
    <label>Customer:
        <select name="customer_id">
            <option value="">Walk-in</option>
            <?php foreach($customers as $c): ?>
                <option value="<?= $c['customer_id'] ?>" <?= ($sale['customer_id']==$c['customer_id'])?'selected':'' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <label>Payment Method:
        <select name="payment_method" required>
            <option value="cash" <?= $sale['payment_method']=='cash'?'selected':'' ?>>Cash</option>
            <option value="card" <?= $sale['payment_method']=='card'?'selected':'' ?>>Card</option>
            <option value="mobile_money" <?= $sale['payment_method']=='mobile_money'?'selected':'' ?>>Mobile Money</option>
            <option value="bank" <?= $sale['payment_method']=='bank'?'selected':'' ?>>Bank</option>
        </select>
    </label><br><br>

    <label>Sales Channel:
        <select name="sales_channel" required>
            <option value="store" <?= $sale['sales_channel']=='store'?'selected':'' ?>>Store</option>
            <option value="online" <?= $sale['sales_channel']=='online'?'selected':'' ?>>Online</option>
            <option value="whatsapp" <?= $sale['sales_channel']=='whatsapp'?'selected':'' ?>>WhatsApp</option>
        </select>
    </label><br><br>

    <label>Discount:
        <input type="number" name="discount" value="<?= htmlspecialchars($sale['discount']) ?>" min="0" step="0.01">
    </label><br><br>

    <h3>Items</h3>
    <table id="sale-items-table" border="1" cellpadding="5">
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Remove</th>
        </tr>
    </table>
    <button type="button" onclick="addRow()">Add Product</button><br><br>

    <button type="submit">Update Sale</button>
</form>

<script>
let rowIndex = 0;
const products = <?= json_encode($products); ?>;

// Populate existing sale items
<?php foreach($sale['items'] as $item): ?>
addRow(<?= $item['product_id'] ?>, <?= $item['quantity'] ?>, <?= $item['unit_price'] ?>);
<?php endforeach; ?>

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
<td><button type="button" onclick="removeRow(this)">Remove</button></td>
`;
    rowIndex++;
}

function removeRow(btn) { 
    btn.closest('tr').remove(); 
}
</script>

<?php include '../../includes/footer.php'; ?>
