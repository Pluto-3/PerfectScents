<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';
require_login();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sale_id = (int)($_POST['sale_id'] ?? 0);
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    $reason = trim($_POST['reason'] ?? '');

    if (!$sale_id) $errors[] = "Sale is required.";
    if (!$product_id) $errors[] = "Product is required.";
    if ($quantity <= 0) $errors[] = "Quantity must be greater than 0.";
    if (!$reason) $errors[] = "Reason is required.";

    if (empty($errors)) {
        try {
            add_return($pdo, $sale_id, $product_id, $quantity, $reason);
            $success = "Return added successfully.";
            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}

$sales = $pdo->query("SELECT sale_id FROM sales ORDER BY sale_date DESC")->fetchAll(PDO::FETCH_ASSOC);
$products = $pdo->query("SELECT product_id, name FROM products WHERE status = 'active'")->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
?>

<main class="main-content">

    <div class="card mb-sm">
        <h2>Add Product Return</h2>
    </div>

    <?php if ($errors): ?>
        <div class="card mb-md">
            <ul style="color:red; margin:0; padding:1em;">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="card mb-md">
            <p style="color:green; margin:0; padding:1em;"><?= htmlspecialchars($success) ?></p>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="post" class="form-grid">

            <div class="form-group">
                <label for="sale_id">Sale ID <span class="text-danger">*</span></label>
                <select id="sale_id" name="sale_id" required>
                    <option value="">--Select Sale--</option>
                    <?php foreach ($sales as $s): ?>
                        <option value="<?= $s['sale_id'] ?>"><?= $s['sale_id'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="product_id">Product <span class="text-danger">*</span></label>
                <select id="product_id" name="product_id" required>
                    <option value="">--Select Product--</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity <span class="text-danger">*</span></label>
                <input type="number" id="quantity" name="quantity" min="1" required>
            </div>

            <div class="form-group">
                <label for="reason">Reason <span class="text-danger">*</span></label>
                <textarea id="reason" name="reason" rows="3" required></textarea>
            </div>

            <div class="form-actions mt-sm">
                <button type="submit" class="btn btn-primary">Add Return</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>

        </form>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
