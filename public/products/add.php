<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $brand = trim($_POST['brand'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $size_ml = $_POST['size_ml'] !== '' ? (float)$_POST['size_ml'] : null;
    $cost_price = (float)($_POST['cost_price'] ?? 0);
    $retail_price = (float)($_POST['retail_price'] ?? 0);
    $supplier_id = $_POST['supplier_id'] !== '' ? (int)$_POST['supplier_id'] : null;
    $status = $_POST['status'] ?? 'active';
    $description = trim($_POST['description'] ?? '');

    if (!$name) $errors[] = "Product name is required.";
    if ($cost_price <= 0) $errors[] = "Cost price must be greater than zero.";
    if ($retail_price <= 0) $errors[] = "Retail price must be greater than zero.";

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
?>

<?php include '../../includes/header.php'; ?>

<main class="main-content">

    <!-- Header -->
    <div class="card mb-sm" style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Add New Product</h2>
    </div>

    <!-- Form Card -->
    <div class="card form-card">
        <?php if ($errors): ?>
            <div class="alert alert-danger mb-md">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success mb-md">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-grid">

            <div class="form-group">
                <label for="name">Product Name<span class="required">*</span></label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="brand">Brand</label>
                <input type="text" id="brand" name="brand">
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" id="category" name="category">
            </div>

            <div class="form-group">
                <label for="size_ml">Size (ml)</label>
                <input type="number" id="size_ml" name="size_ml" step="0.01">
            </div>

            <div class="form-group">
                <label for="cost_price">Cost Price<span class="required">*</span></label>
                <input type="number" id="cost_price" name="cost_price" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="retail_price">Retail Price<span class="required">*</span></label>
                <input type="number" id="retail_price" name="retail_price" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="supplier_id">Supplier</label>
                <select id="supplier_id" name="supplier_id">
                    <option value="">-- Select Supplier --</option>
                    <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s['supplier_id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="active">Active</option>
                    <option value="discontinued">Discontinued</option>
                </select>
            </div>

            <div class="form-group full-width">
                <label for="description">Description<span class="required">*</span></label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>

            <div class="form-actions mt-md">
                <button type="submit" class="btn btn-primary">Save Product</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>

        </form>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
