<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = (int)($_GET['id'] ?? 0);
$customer = get_customer_by_id($pdo, $id);

if (!$customer) die("Customer not found.");

include '../../includes/header.php';
?>

<main class="main-content">

    <!-- Page Header -->
    <div class="card mb-sm">
        <h2>Customer Details For: <?= htmlspecialchars($customer['name']) ?></h2>
    </div>

    <!-- Customer Details Table -->
    <div class="card">
        <table class="table">
            <tbody>
                <tr>
                    <th>Name</th>
                    <td><?= htmlspecialchars($customer['name']) ?></td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td><?= htmlspecialchars($customer['phone']) ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= htmlspecialchars($customer['email']) ?></td>
                </tr>
                <tr>
                    <th>Region</th>
                    <td><?= htmlspecialchars($customer['region']) ?></td>
                </tr>
                <tr>
                    <th>Source</th>
                    <td><?= htmlspecialchars($customer['source']) ?></td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td><?= htmlspecialchars($customer['created_at']) ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Action Buttons -->
        <div class="flex gap-md mt-sm">
            <a href="edit.php?id=<?= $id ?>" class="btn btn-primary">Edit</a>
            <a href="delete.php?id=<?= $id ?>" class="btn btn-danger"
               onclick="return confirm('Are you sure you want to delete this customer?');">
               Delete
            </a>
            <a href="index.php" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
