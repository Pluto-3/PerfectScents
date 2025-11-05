<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

if (!isset($_GET['id'])) die("Supplier ID not specified.");

$supplier_id = (int)$_GET['id'];
$supplier = get_supplier_by_id($pdo, $supplier_id);

if (!$supplier) die("Supplier not found.");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        delete_supplier($pdo, $supplier_id);
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<?php include '../../includes/header.php'; ?>

<main class="main-content">

    <div class="card mb-sm">
        <h2>Delete Supplier #<?= $supplier_id ?></h2>
        <p>Are you sure you want to delete the supplier <strong><?= htmlspecialchars($supplier['name']) ?></strong>?</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error mb-sm">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <div class="form-actions mt-sm">
                <button type="submit" class="btn btn-danger">Yes, Delete</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
