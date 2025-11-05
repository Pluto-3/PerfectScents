<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

// Get sale ID
$sale_id = $_GET['id'] ?? null;
if (!$sale_id) die("Sale ID is required.");

// Fetch sale info
$stmt = $pdo->prepare("
    SELECT s.*, c.name AS customer_name 
    FROM sales s
    LEFT JOIN customers c ON s.customer_id = c.customer_id
    WHERE s.sale_id = ?
");
$stmt->execute([$sale_id]);
$sale = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$sale) die("Sale not found.");

// Fetch sale items
$stmt = $pdo->prepare("
    SELECT si.*, p.name AS product_name, p.brand, p.category, p.size_ml 
    FROM sale_items si
    JOIN products p ON si.product_id = p.product_id
    WHERE si.sale_id = ?
");
$stmt->execute([$sale_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
?>

<h2>Sale Details - #<?= htmlspecialchars($sale['sale_id']) ?></h2>

<!-- ===== Sale Info Block ===== -->
<section style="border:1px solid #ccc; padding:10px; margin-bottom:15px;">
    <h3>Sale Information</h3>
    <p><strong>Customer:</strong> <?= htmlspecialchars($sale['customer_name'] ?? 'Walk-in') ?></p>
    <p><strong>Sale Date:</strong> <?= htmlspecialchars($sale['sale_date']) ?></p>
    <p><strong>Payment Method:</strong> <?= htmlspecialchars($sale['payment_method']) ?></p>
    <p><strong>Sales Channel:</strong> <?= htmlspecialchars($sale['sales_channel']) ?></p>
    <p><strong>Discount:</strong> <?= number_format($sale['discount'], 2) ?></p>
</section>

<!-- ===== Items Block ===== -->
<section style="border:1px solid #ccc; padding:10px; margin-bottom:15px;">
    <h3>Items in this Sale</h3>

    <?php if ($items): 
        $total_amount = 0;
        foreach ($items as $item): 
            $subtotal = $item['quantity'] * $item['unit_price'];
            $total_amount += $subtotal;
    ?>
        <div style="border:1px solid #eee; padding:8px; margin-bottom:8px;">
            <p><strong>Product:</strong> <?= htmlspecialchars($item['product_name']) ?></p>
            <p><strong>Brand:</strong> <?= htmlspecialchars($item['brand']) ?></p>
            <p><strong>Category:</strong> <?= htmlspecialchars($item['category']) ?></p>
            <p><strong>Size:</strong> <?= htmlspecialchars($item['size_ml']) ?> ml</p>
            <p><strong>Quantity:</strong> <?= (int)$item['quantity'] ?></p>
            <p><strong>Unit Price:</strong> <?= number_format($item['unit_price'], 2) ?></p>
            <p><strong>Subtotal:</strong> <?= number_format($subtotal, 2) ?></p>
        </div>
    <?php endforeach; ?>
    <?php else: ?>
        <p>No items found for this sale.</p>
    <?php endif; ?>
</section>

<!-- ===== Total Block ===== -->
<section style="border:1px solid #ccc; padding:10px;">
    <h3>Total</h3>
    <p><strong>Total Amount (after discount):</strong> <?= number_format($total_amount - $sale['discount'], 2) ?></p>
</section>

<p>
    <a href="index.php">Back to Sales</a>
</p>

<?php include '../../includes/footer.php'; ?>
