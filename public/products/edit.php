<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = $_GET['id'] ?? null;
if (!$id) die("Product ID missing.");

$product = get_product($pdo, $id);
if (!$product) die("Product not found.");

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $supplier_id = $_POST['supplier_id'];
    $cost_price = $_POST['cost_price'];
    $unit_price = $_POST['unit_price'];
    $description = trim($_POST['description']);

    if(update_product($pdo, $id, $name, $supplier_id, $cost_price, $unit_price, $description)) {
        header('Location: index.php');
        exit();
    } else {
        $message = "Error updating product.";
    }
}
?>

<?php include '../../includes/header.php'; ?>
<h2>Edit Product</h2>
<?php if($message): ?>
    <p style="color:red;"><?= $message ?></p>
<?php endif; ?>
<form method="post">
    <label>Name:</label><input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br>
    <label>Supplier ID:</label><input type="number" name="supplier_id" value="<?= $product['supplier_id'] ?>" required><br>
    <label>Cost Price:</label><input type="number" step="0.01" name="cost_price" value="<?= $product['cost_price'] ?>" required><br>
    <label>Unit Price:</label><input type="number" step="0.01" name="unit_price" value="<?= $product['unit_price'] ?>" required><br>
    <label>Description:</label><textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea><br>
    <button type="submit">Update Product</button>
</form>
<?php include '../../includes/footer.php'; ?>
