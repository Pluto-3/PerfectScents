<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$return_id = $_GET['id'] ?? null;
if (!$return_id) die("Return ID is required.");

$r = get_return_by_id($pdo, (int)$return_id);
if (!$r) die("Return not found.");

// Get product and sale details
$product = $pdo->prepare("SELECT name FROM products WHERE product_id = ?");
$product->execute([$r['product_id']]);
$product_name = $product->fetchColumn();

$sale = $pdo->prepare("SELECT sale_date, total_amount FROM sales WHERE sale_id = ?");
$sale->execute([$r['sale_id']]);
$sale_info = $sale->fetch(PDO::FETCH_ASSOC);

include '../../includes/header.php';
?>

<main class="main-content">

    <div class="card mb-sm">
        <h2>Return Details #<?= (int)$r['return_id'] ?></h2>
    </div>

    <div class="card mb-md">
        <table class="table">
            <tr>
                <th>Return ID</th>
                <td><?= (int)$r['return_id'] ?></td>
            </tr>
            <tr>
                <th>Sale ID</th>
                <td><?= (int)$r['sale_id'] ?></td>
            </tr>
            <tr>
                <th>Product</th>
                <td><?= htmlspecialchars($product_name) ?></td>
            </tr>
            <tr>
                <th>Quantity</th>
                <td><?= (int)$r['quantity'] ?></td>
            </tr>
            <tr>
                <th>Reason</th>
                <td><?= htmlspecialchars($r['reason']) ?></td>
            </tr>
            <tr>
                <th>Return Date</th>
                <td><?= htmlspecialchars($r['return_date']) ?></td>
            </tr>
            <tr>
                <th>Sale Date</th>
                <td><?= htmlspecialchars($sale_info['sale_date']) ?></td>
            </tr>
            <tr>
                <th>Sale Total</th>
                <td><?= number_format($sale_info['total_amount'], 2) ?></td>
            </tr>
        </table>
    </div>

    <div class="card mb-sm">
        <a href="edit.php?id=<?= $r['return_id'] ?>" class="btn btn-primary">Edit</a>
        <a href="delete.php?id=<?= $r['return_id'] ?>" class="btn btn-danger">Delete</a>
        <a href="index.php" class="btn btn-secondary">Back to Returns</a>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
