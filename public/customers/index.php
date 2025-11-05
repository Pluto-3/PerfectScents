<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$customers = get_all_customers($pdo);

include '../../includes/header.php';
?>

<main class="main-content">

    <!-- Page Header -->
    <div class="card mb-sm" style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Customers</h2>
    </div>

    <div class="card mb-md flex end">
        <a href="add.php" class="btn btn-primary">Add New Customer</a>
    </div>

    <!-- Customers Table -->
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Region</th>
                    <th>Source</th>
                    <th>Created At</th>
                    <th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($customers): ?>
                    <?php foreach ($customers as $c): ?>
                        <tr>
                            <td><?= (int)$c['customer_id'] ?></td>
                            <td><?= htmlspecialchars($c['name']) ?></td>
                            <td><?= htmlspecialchars($c['phone']) ?></td>
                            <td><?= htmlspecialchars($c['email']) ?></td>
                            <td><?= htmlspecialchars($c['region']) ?></td>
                            <td><?= htmlspecialchars($c['source']) ?></td>
                            <td><?= htmlspecialchars($c['created_at']) ?></td>
                            <td class="flex center gap-md">
                                <a href="view.php?id=<?= (int)$c['customer_id'] ?>" class="btn btn-secondary">View</a>
                                <a href="edit.php?id=<?= (int)$c['customer_id'] ?>" class="btn btn-primary">Edit</a>
                                <a href="delete.php?id=<?= (int)$c['customer_id'] ?>" class="btn btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this customer?');">
                                   Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="center text-muted">No customers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
