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

?>

<?php include '../../includes/header.php'; ?>

<h2>Customer Details</h2>

<ul>
    <li><strong>Name:</strong> <?= htmlspecialchars($customer['name']) ?></li>
    <li><strong>Phone:</strong> <?= htmlspecialchars($customer['phone']) ?></li>
    <li><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></li>
    <li><strong>Region:</strong> <?= htmlspecialchars($customer['region']) ?></li>
    <li><strong>Source:</strong> <?= htmlspecialchars($customer['source']) ?></li>
    <li><strong>Created At:</strong> <?= htmlspecialchars($customer['created_at']) ?></li>
</ul>

<a href="edit.php?id=<?= $id ?>">Edit</a> | 
<a href="delete.php?id=<?= $id ?>" onclick="return confirm('Delete this customer?');">Delete</a>

<?php include '../../includes/footer.php'; ?>
