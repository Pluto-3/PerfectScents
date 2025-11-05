<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$suppliers = get_all_suppliers($pdo);
?>

<?php include '../../includes/header.php'; ?>

<main class="main-content">

    <!-- Page Header -->
    <div class="card mb-sm" style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Suppliers</h2>
    </div>

    <div class="card mb-md flex end">
        <a href="add.php" class="btn btn-primary">Add New Supplier</a>
    </div>

    <!-- Suppliers Table -->
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Contact Person</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Reliability Score</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($suppliers): ?>
                    <?php foreach($suppliers as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['supplier_id']) ?></td>
                            <td><?= htmlspecialchars($s['name']) ?></td>
                            <td><?= htmlspecialchars($s['contact_person']) ?></td>
                            <td><?= htmlspecialchars($s['phone']) ?></td>
                            <td><?= htmlspecialchars($s['email']) ?></td>
                            <td><?= htmlspecialchars($s['address']) ?></td>
                            <td><?= number_format($s['reliability_score'], 2) ?></td>
                            <td class="flex center gap-md">
                                <a href="view.php?id=<?= $s['supplier_id'] ?>" class="btn btn-secondary">View</a>
                                <a href="edit.php?id=<?= $s['supplier_id'] ?>" class="btn btn-primary">Edit</a>
                                <a href="delete.php?id=<?= $s['supplier_id'] ?>" class="btn btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="center text-muted">No suppliers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
