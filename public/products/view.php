<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

// Validate and fetch product ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Product ID missing or invalid.");
}

$product_id = (int)$_GET['id'];
$product = get_product_by_id($pdo, $product_id);

if (!$product) {
    die("Product not found.");
}

// Fetch supplier name if linked
$supplier_name = null;
if (!empty($product['supplier_id'])) {
    $stmt = $pdo->prepare("SELECT name FROM suppliers WHERE supplier_id = :id");
    $stmt->execute(['id' => $product['supplier_id']]);
    $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
    $supplier_name = $supplier ? $supplier['name'] : null;
}

include '../../includes/header.php';
?>

<div class="card mb-sm" style="display:flex; justify-content:space-between; align-items:center;">
<h2>Product Details</h2>
</div>

<table class="details-table">
    <tr>
        <th>Name:</th>
        <td><?= htmlspecialchars($product['name']) ?></td>
    </tr>
    <tr>
        <th>Brand:</th>
        <td><?= htmlspecialchars($product['brand'] ?: '-') ?></td>
    </tr>
    <tr>
        <th>Category:</th>
        <td><?= htmlspecialchars($product['category'] ?: '-') ?></td>
    </tr>
    <tr>
        <th>Size (ml):</th>
        <td><?= $product['size_ml'] ? htmlspecialchars($product['size_ml']) : '-' ?></td>
    </tr>
    <tr>
        <th>Cost Price:</th>
        <td><?= number_format($product['cost_price'], 2) ?></td>
    </tr>
    <tr>
        <th>Retail Price:</th>
        <td><?= number_format($product['retail_price'], 2) ?></td>
    </tr>
    <tr>
        <th>Supplier:</th>
        <td><?= htmlspecialchars($supplier_name ?: '-') ?></td>
    </tr>
    <tr>
        <th>Status:</th>
        <td><?= ucfirst(htmlspecialchars($product['status'])) ?></td>
    </tr>
    <tr>
        <th>Description:</th>
        <td><?= nl2br(htmlspecialchars($product['description'])) ?></td>
    </tr>
    <tr>
        <th>Created At:</th>
        <td><?= htmlspecialchars($product['created_at']) ?></td>
    </tr>
</table>

<div class="card mb-sm" style="display:flex; justify-content:space-between;">
    <a class="btn btn-primary" href="edit.php?id=<?= $product['product_id'] ?>">Edit</a>
    <a class="btn btn-danger" href="delete.php?id=<?= $product['product_id'] ?>"
       onclick="return confirm('Are you sure you want to delete this product?');">
       Delete
    </a>
    <a class="btn btn-secondary" href="index.php">Back to Products</a>
</div>

<?php include '../../includes/footer.php'; ?>
