<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = $_GET['id'] ?? null;
if (!$id) die("Product ID missing.");

$product = get_product($pdo, $id);
if (!$product) die("Product not found.");
?>

<?php include '../../includes/header.php'; ?>
<h2>Product Details</h2>
<ul>
    <li><strong>ID:</strong> <?= $product['product_id'] ?></li>
    <li><strong>Name:</strong> <?= htmlspecialchars($product['name']) ?></li>
    <li><strong>Supplier ID:</strong> <?= $product['supplier_id'] ?></li>
    <li><strong>Cost Price:</strong> <?= CURRENCY . ' ' . number_format($product['cost_price'],2) ?></li>
    <li><strong>Unit Price:</strong> <?= CURRENCY . ' ' . number_format($product['unit_price'],2) ?></li>
    <li><strong>Description:</strong> <?= htmlspecialchars($product['description']) ?></li>
</ul>
<a href="edit.php?id=<?= $product['product_id'] ?>">Edit Product</a>
<?php include '../../includes/footer.php'; ?>
