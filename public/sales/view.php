<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

if (!isset($_GET['id'])) die("Sale ID not specified.");
$sale_id = (int)$_GET['id'];

// Fetch sale with items
$sale = get_sale_by_id($pdo, $sale_id);
if (!$sale) die("Sale not found.");
?>

<?php include '../../includes/header.php'; ?>

<h2>Sale #<?= $sale['sale_id'] ?></h2>

<p><strong>Date:</strong> <?= $sale['sale_date'] ?></p>
<p><strong>Customer:</strong> <?= htmlspecialchars($sale['customer_name'] ?? 'Walk-in') ?></p>
<p><strong>Payment Method:</strong> <?= htmlspecialchars($sale['payment_method']) ?></p>
<p><strong>Sales Channel:</strong> <?= htmlspecialchars($sale['sales_channel']) ?></p>
<p><strong>Discount:</strong> <?= number_format($sale['discount'], 2) ?></p>

<h3>Items</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>Product</th>
        <th>Quantity</th>
        <th>Unit Price</th>
        <th>Subtotal</th>
        <th>Cost Price</th>
        <th>Margin</th>
    </tr>
    <?php
    $total = 0;
    foreach ($sale['items'] as $item):
        $subtotal = $item['quantity'] * $item['retail_price'];
        $margin = $subtotal - ($item['quantity'] * $item['cost_price']);
        $total += $subtotal;
    ?>
    <tr>
        <td><?= htmlspecialchars($item['product_name']) ?></td>
        <td><?= $item['quantity'] ?></td>
        <td><?= number_format($item['retail_price'], 2) ?></td>
        <td><?= number_format($subtotal, 2) ?></td>
        <td><?= number_format($item['cost_price'], 2) ?></td>
        <td><?= number_format($margin, 2) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<p><strong>Total Amount:</strong> <?= number_format($total - $sale['discount'], 2) ?></p>

<p>
    <a href="edit.php?id=<?= $sale_id ?>">Edit Sale</a> |
    <a href="delete.php?id=<?= $sale_id ?>" onclick="return confirm('Delete this sale?')">Delete Sale</a> |
    <a href="index.php">Back to Sales</a>
</p>

<?php include '../../includes/footer.php'; ?>
