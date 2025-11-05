<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';
require_login();

$purchase_id = $_GET['id'] ?? null;

if (!$purchase_id || !is_numeric($purchase_id)) {
    die('Invalid purchase ID.');
}

// Fetch purchase to display basic info
$purchase = get_purchase_by_id($pdo, $purchase_id);
if (!$purchase) die("Purchase not found.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // User confirmed deletion
    try {
        delete_purchase($pdo, $purchase_id); // Also reverses inventory
        log_action($pdo, $_SESSION['user_id'], 'delete', 'purchases', $purchase_id, 'Deleted purchase and associated items');
        header('Location: index.php?msg=Purchase+deleted+successfully');
        exit;
    } catch (Exception $e) {
        $error = "Error deleting purchase: " . htmlspecialchars($e->getMessage());
    }
}

include '../../includes/header.php';
?>

<main class="main-content">

    <div class="card mb-sm">
        <h2>Delete Purchase #<?= (int)$purchase['purchase_id'] ?></h2>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error mb-sm"><?= $error ?></div>
    <?php endif; ?>

    <div class="card">
        <p>Are you sure you want to delete this purchase from <strong><?= htmlspecialchars($purchase['supplier_name']) ?></strong> dated <strong><?= htmlspecialchars($purchase['purchase_date']) ?></strong>?</p>

        <form method="post" class="flex gap-md">
            <button type="submit" class="btn btn-danger">Yes, Delete</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
