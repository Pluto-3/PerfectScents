<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

if (!isset($_GET['id'])) die("Product ID missing.");
$product_id = (int)$_GET['id'];
$product = get_product_by_id($pdo, $product_id);
if (!$product) die("Product not found.");

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $brand = trim($_POST['brand'] ?: null);
    $category = trim($_POST['category'] ?: null);
    $size_ml = $_POST['size_ml'] !== '' ? (float)$_POST['size_ml'] : null;
    $cost_price = (float)$_POST['cost_price'];
    $retail_price = (float)$_POST['retail_price'];
    $supplier_id = $_POST['supplier_id'] !== '' ? (int)$_POST['supplier_id'] : null;
    $status = $_POST['status'];
    $description = trim($_POST['description']);

    if (!$name) $errors[] = "Product name is required.";
    if ($cost_price <= 0) $errors[] = "Cost price must be positive.";
    if ($retail_price <= 0) $errors[] = "Retail price must be positive.";

    if (!$errors) {
        try {
            edit_product($pdo, $product_id, $name, $brand, $category, $size_ml, $cost_price, $retail_price, $supplier_id, $status, $description);
            $success = "Product updated successfully.";
            $product = get_product_by_id($pdo, $product_id);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}

// Fetch suppliers for dropdown
$suppliers = $pdo->query("SELECT supplier_id, name FROM suppliers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
?>

<h2>Edit Product</h2>

<?php if ($errors): ?>
    <ul style="color:red;">
        <?php foreach ($errors as $err) echo "<li>$err</li>"; ?>
    </ul>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="post">
    <label>Name:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br>

    <label>Brand:</label>
    <input type="text" name="brand" value="<?= htmlspecialchars($product['brand']) ?>"><br>

    <label>Category:</label>
    <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>"><br>

    <label>Size (ml):</label>
    <input type="number" step="0.01" name="size_ml" value="<?= htmlspecialchars($product['size_ml']) ?>"><br>

    <label>Cost Price:</label>
    <input type="number" step="0.01" name="cost_price" value="<?= htmlspecialchars($product['cost_price']) ?>" required><br>

    <label>Retail Price:</label>
    <input type="number" step="0.01" name="retail_price" value="<?= htmlspecialchars($product['retail_price']) ?>" required><br>

    <label>Supplier:</label>
    <select name="supplier_id">
        <option value="">-- Select Supplier --</option>
        <?php foreach ($suppliers as $s): ?>
            <option value="<?= $s['supplier_id'] ?>" <?= $s['supplier_id'] == $product['supplier_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label>Status:</label>
    <select name="status">
        <option value="active" <?= $product['status'] === 'active' ? 'selected' : '' ?>>Active</option>
        <option value="discontinued" <?= $product['status'] === 'discontinued' ? 'selected' : '' ?>>Discontinued</option>
    </select><br>

    <label>Description:</label>
    <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea><br>

    <button type="submit">Update Product</button>
</form>

<a href="index.php">Cancel</a>

<?php include '../../includes/footer.php'; ?>
