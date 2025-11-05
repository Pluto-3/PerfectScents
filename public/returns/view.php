<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';
require_login();

if (!isset($_GET['id'])) {
    die("Return ID is missing.");
}

$return_id = (int)$_GET['id'];
$r = get_return_by_id($pdo, $return_id);
if (!$r) {
    die("Return not found.");
}

// Get product and sale details
$product = $pdo->prepare("SELECT name FROM products WHERE product_id = ?");
$product->execute([$r['product_id']]);
$product_name = $product->fetchColumn();

$sale = $pdo->prepare("SELECT sale_date, total_amount FROM sales WHERE sale_id = ?");
$sale->execute([$r['sale_id']]);
$sale_info = $sale->fetch(PDO::FETCH_ASSOC);

include '../../includes/header.php';
?>

<h2>Return Details</h2>

<table>
    <tr><th>Return ID</th><td><?= htmlspecialchars($r['return_id']) ?></td></tr>
    <tr><th>Sale ID</th><td><?= htmlspecialchars($r['sale_id']) ?></td></tr>
    <tr><th>Product</th><td><?= htmlspecialchars($product_name) ?></td></tr>
    <tr><th>Quantity</th><td><?= htmlspecialchars($r['quantity']) ?></td></tr>
    <tr><th>Reason</th><td><?= htmlspecialchars($r['reason']) ?></td></tr>
    <tr><th>Return Date</th><td><?= htmlspecialchars($r['return_date']) ?></td></tr>
    <tr><th>Sale Date</th><td><?= htmlspecialchars($sale_info['sale_date']) ?></td></tr>
    <tr><th>Sale Total</th><td><?= htmlspecialchars($sale_info['total_amount']) ?></td></tr>
</table>

<a href="edit_return.php?id=<?= $r['return_id'] ?>">Edit</a> |
<a href="delete_return.php?id=<?= $r['return_id'] ?>" 
   onclick="return confirm('Are you sure you want to delete this return?');">Delete</a> |
<a href="index.php">Back to List</a>

<?php include '../../includes/footer.php'; ?>
