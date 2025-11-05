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

<main class="main-content">

    <!-- ===== Sale Header Info ===== -->
    <div class="card mb-sm">
        <h2 class="card-title">Sale Details - #<?= htmlspecialchars($sale['sale_id']) ?></h2>
        <div class="sale-info">
            <p><strong>Customer:</strong> <?= htmlspecialchars($sale['customer_name'] ?? 'Walk-in') ?></p>
            <p><strong>Sale Date:</strong> <?= htmlspecialchars($sale['sale_date']) ?></p>
            <p><strong>Payment Method:</strong> <?= htmlspecialchars($sale['payment_method']) ?></p>
            <p><strong>Sales Channel:</strong> <?= htmlspecialchars($sale['sales_channel']) ?></p>
            <p><strong>Discount:</strong> <?= number_format($sale['discount'], 2) ?></p>
        </div>
    </div>

    <!-- ===== Items Table ===== -->
    <div class="card mb-sm">
        <h3 class="card-title">Items in this Sale</h3>

        <?php if ($items): 
            $total_amount = 0;
        ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Size (ml)</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): 
                    $subtotal = $item['quantity'] * $item['unit_price'];
                    $total_amount += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= htmlspecialchars($item['brand']) ?></td>
                    <td><?= htmlspecialchars($item['category']) ?></td>
                    <td><?= htmlspecialchars($item['size_ml']) ?></td>
                    <td><?= (int)$item['quantity'] ?></td>
                    <td><?= number_format($item['unit_price'], 2) ?></td>
                    <td><?= number_format($subtotal, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php else: ?>
        <div class="alert alert-info">No items found for this sale.</div>
        <?php endif; ?>
    </div>

    <!-- ===== Total ===== -->
    <div class="card card-total mb-sm">
        <p><strong>Subtotal:</strong> <?= number_format($total_amount, 2) ?></p>
        <p><strong>Discount:</strong> <?= number_format($sale['discount'], 2) ?></p>
        <hr>
        <p><strong>Total Amount:</strong> <?= number_format($total_amount - $sale['discount'], 2) ?></p>
    </div>


    <!-- ===== Back Button ===== -->
    <div class="mt-sm">
        <a href="index.php" class="btn btn-secondary">Back to Sales</a>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>

