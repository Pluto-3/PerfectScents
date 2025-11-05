<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

// Only accept GET to show confirmation, POST to execute deletion
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Product ID missing or invalid.");
}

$product_id = (int)$_GET['id'];
$product = get_product_by_id($pdo, $product_id);
if (!$product) {
    die("Product not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        delete_product($pdo, $product_id);
        header("Location: index.php?msg=Product+deleted+successfully");
        exit;
    } catch (Exception $e) {
        die("Error deleting product: " . htmlspecialchars($e->getMessage()));
    }
}

include '../../includes/header.php';
?>

<div class="card mb-sm" style="display:flex; justify-content:space-between; align-items:center;">

    <h2>Confirm Deletion</h2>
    <p>Are you sure you want to delete the product <strong><?= htmlspecialchars($product['name']) ?></strong>?</p>

    <form method="post">
        <button type="submit" style="color:white; background-color:red;">Yes, Delete</button>
        <a class="btn btn-secondary" href="index.php">Cancel</a>
    </form>

</div>

<?php include '../../includes/footer.php'; ?>
