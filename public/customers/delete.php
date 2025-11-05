<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = (int)($_GET['id'] ?? 0);
$customer = get_customer_by_id($pdo, $id);

if (!$customer) {
    die("Customer not found.");
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (delete_customer($pdo, $id)) {
        $success = "Customer deleted successfully.";
    } else {
        $errors[] = "Failed to delete customer.";
    }
}

include '../../includes/header.php';
?>

<main class="main-content">

    <div class="card mb-sm">
        <h2>Delete Customer #<?= htmlspecialchars($customer['customer_id']) ?></h2>
    </div>

    <?php if ($errors): ?>
        <div class="card mb-md" style="color:red;">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="card" style="color:green;">
            <p><?= htmlspecialchars($success) ?></p>
            <a href="index.php" class="btn btn-primary">Back to Customers</a>
        </div>
    <?php else: ?>
        <div class="card mb-md">
            <p>Are you sure you want to delete the following customer?</p>
            <table class="table">
                <tr><th>ID</th><td><?= htmlspecialchars($customer['customer_id']) ?></td></tr>
                <tr><th>Name</th><td><?= htmlspecialchars($customer['name']) ?></td></tr>
                <tr><th>Phone</th><td><?= htmlspecialchars($customer['phone']) ?></td></tr>
                <tr><th>Email</th><td><?= htmlspecialchars($customer['email']) ?></td></tr>
                <tr><th>Region</th><td><?= htmlspecialchars($customer['region']) ?></td></tr>
                <tr><th>Source</th><td><?= htmlspecialchars($customer['source']) ?></td></tr>
                <tr><th>Created At</th><td><?= htmlspecialchars($customer['created_at']) ?></td></tr>
            </table>

            <form method="POST" class="mt-sm flex gap-md">
                <button type="submit" class="btn btn-danger">Confirm Delete</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    <?php endif; ?>

</main>

<?php include '../../includes/footer.php'; ?>
