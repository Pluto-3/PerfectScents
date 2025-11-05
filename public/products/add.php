<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

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
            add_product($pdo, $name, $brand, $category, $size_ml, $cost_price, $retail_price, $supplier_id, $status, $description);
            $success = "Product added successfully.";
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}

// Fetch suppliers for dropdown
$suppliers = $pdo->query("SELECT supplier_id, name FROM suppliers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
?>

<h2>Add New Product</h2>

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
    <input type="text" name="name" required><br>

    <label>Brand:</label>
    <input type="text" name="brand"><br>

    <label>Category:</label>
    <input type="text" name="category"><br>

    <label>Size (ml):</label>
    <input type="number" step="0.01" name="size_ml"><br>

    <label>Cost Price:</label>
    <input type="number" step="0.01" name="cost_price" required><br>

    <label>Retail Price:</label>
    <input type="number" step="0.01" name="retail_price" required><br>

    <label>Supplier:</label>
    <select name="supplier_id">
        <option value="">-- Select Supplier --</option>
        <?php foreach ($suppliers as $s): ?>
            <option value="<?= $s['supplier_id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
        <?php endforeach; ?>
    </select><br>

    <label>Status:</label>
    <select name="status">
        <option value="active">Active</option>
        <option value="discontinued">Discontinued</option>
    </select><br>

    <label>Description:</label>
    <textarea name="description" required></textarea><br>

    <button type="submit">Add Product</button>
</form>

<a href="index.php">Cancel</a>

<?php include '../../includes/footer.php'; ?>
