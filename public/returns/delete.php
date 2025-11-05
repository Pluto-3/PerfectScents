<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$return_id = $_GET['id'] ?? null;
if (!$return_id || !is_numeric($return_id)) {
    die("Invalid return ID.");
}

$return = get_return_by_id($pdo, $return_id);
if (!$return) die("Return not found.");

// Fetch product name and sale info
$product_stmt = $pdo->prepare("SELECT name FROM products WHERE product_id = ?");
$product_stmt->execute([$return['product_id']]);
$product_name = $product_stmt->fetchColumn() ?: '-';

$sale_stmt = $pdo->prepare("SELECT sale_date, total_amount FROM sales WHERE sale_id = ?");
$sale_stmt->execute([$return['sale_id']]);
$sale_info = $sale_stmt->fetch(PDO::FETCH_ASSOC) ?: ['sale_date'=>'-', 'total_amount'=>'-'];

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        delete_return($pdo, $return_id);
        $success = "Return deleted successfully.";
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}

include '../../includes/header.php';
?>

<main class="main-content">

    <div class="card mb-sm">
        <h2>Delete Return #<?= (int)$return_id ?></h2>
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
            <p class="success-msg"><?= htmlspecialchars($success) ?></p>
            <a href="index.php" class="btn btn-primary">Back to Returns List</a>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-content">
                <p>Are you sure you want to delete the following return?</p>

                <table class="table mb-sm">
                    <tr><th>Return ID</th><td><?= htmlspecialchars($return['return_id']) ?></td></tr>
                    <tr><th>Sale ID</th><td><?= htmlspecialchars($return['sale_id']) ?></td></tr>
                    <tr><th>Product</th><td><?= htmlspecialchars($product_name) ?></td></tr>
                    <tr><th>Quantity</th><td><?= htmlspecialchars($return['quantity']) ?></td></tr>
                    <tr><th>Reason</th><td><?= htmlspecialchars($return['reason']) ?></td></tr>
                    <tr><th>Return Date</th><td><?= htmlspecialchars($return['return_date']) ?></td></tr>
                    <tr><th>Sale Date</th><td><?= htmlspecialchars($sale_info['sale_date']) ?></td></tr>
                    <tr><th>Sale Total</th><td><?= htmlspecialchars($sale_info['total_amount']) ?></td></tr>
                </table>

                <form method="post" class="flex gap-md mt-sm">
                    <button type="submit" class="btn btn-danger">Confirm Delete</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    <?php endif; ?>

</main>

<?php include '../../includes/footer.php'; ?>
