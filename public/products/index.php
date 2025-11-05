<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$products = get_all_products($pdo);
?>

<?php include '../../includes/header.php'; ?>

<main class="main-content">

    <!-- Header and Action -->
    <div class="card mb-sm" style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Products</h2>
    </div>

    <div class="card mb-md flex end">
        <a href="add.php" class="btn btn-primary">Add New Product</a>
    </div>

    <!-- Products Table Card -->
    <div class="card">
        <?php if (empty($products)): ?>
            <p class="text-muted">No products found.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Size (ml)</th>
                        <th>Cost Price</th>
                        <th>Retail Price</th>
                        <th>Supplier</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= $p['product_id'] ?></td>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td><?= htmlspecialchars($p['brand']) ?></td>
                            <td><?= htmlspecialchars($p['category']) ?></td>
                            <td><?= htmlspecialchars($p['size_ml']) ?></td>
                            <td><?= number_format($p['cost_price'], 2) ?></td>
                            <td><?= number_format($p['retail_price'], 2) ?></td>
                            <td><?= htmlspecialchars($p['supplier_name'] ?: '-') ?></td>
                            <td>
                                <span class="status <?= strtolower($p['status']) ?>">
                                    <?= htmlspecialchars($p['status']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($p['description'] ?: '-') ?></td>
                            <td class="actions">
                                <a href="view.php?id=<?= $p['product_id'] ?>" class="btn btn-secondary">View</a>
                                <a href="edit.php?id=<?= $p['product_id'] ?>" class="btn btn-primary">Edit</a>
                                <a href="delete.php?id=<?= $p['product_id'] ?>" 
                                   class="btn btn-danger"
                                   onclick="return confirm('Delete this product?')">
                                   Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
