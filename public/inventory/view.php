<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = $_GET['id'] ?? null;
$inventory = get_inventory_by_id($pdo, $id);
if (!$inventory) die("Record not found");

include '../../includes/header.php';
?>

<h2>View Inventory Record</h2>

<p><strong>Product:</strong> <?= htmlspecialchars($inventory['product_name']) ?></p>
<p><strong>Stock In:</strong> <?= $inventory['stock_in'] ?></p>
<p><strong>Stock Out:</strong> <?= $inventory['stock_out'] ?></p>
<p><strong>Current Stock:</strong> <?= $inventory['current_stock'] ?></p>
<p><strong>Last Updated:</strong> <?= $inventory['last_updated'] ?></p>

<a href="edit.php?id=<?= $inventory['inventory_id'] ?>">Edit</a> |
<a href="index.php">Back</a>

<?php include '../../includes/footer.php'; ?>
