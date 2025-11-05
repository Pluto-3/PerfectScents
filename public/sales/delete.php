<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

if (!isset($_GET['id'])) die("Sale ID not specified.");
$sale_id = (int)$_GET['id'];

// Fetch sale for display
$sale = get_sale_by_id($pdo, $sale_id);
if (!$sale) die("Sale not found.");

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        delete_sale($pdo, $sale_id);
        $success = "Sale #$sale_id deleted successfully.";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<?php include '../../includes/header.php'; ?>

<main class="main-content">

    <div class="card mb-sm">
        <h2 class="card-title">Delete Sale #<?= $sale_id ?></h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <a href="index.php" class="btn btn-primary mt-sm">Back to Sales</a>
        <?php else: ?>
            <p>Are you sure you want to delete the following sale? This action <strong>cannot</strong> be undone.</p>

            <!-- Sale Info Card -->
            <div class="card mb-sm" style="padding:16px;">
                <p><strong>Customer:</strong> <?= htmlspecialchars($sale['customer_name'] ?? 'Walk-in') ?></p>
                <p><strong>Sale Date:</strong> <?= htmlspecialchars($sale['sale_date']) ?></p>
                <p><strong>Payment Method:</strong> <?= htmlspecialchars($sale['payment_method']) ?></p>
                <p><strong>Sales Channel:</strong> <?= htmlspecialchars($sale['sales_channel']) ?></p>
                <p><strong>Discount:</strong> <?= number_format($sale['discount'], 2) ?></p>
            </div>

            <form method="post">
                <button type="submit" class="btn btn-danger">Confirm Delete</button>
                <a href="index.php?id=<?= $sale_id ?>" class="btn btn-secondary">Cancel</a>
            </form>
        <?php endif; ?>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
