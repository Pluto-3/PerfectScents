<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

if (!isset($_GET['id'])) {
    die("Return ID missing.");
}

$return_id = (int)$_GET['id'];
$return = get_return_by_id($pdo, $return_id);

if (!$return) {
    die("Return not found.");
}

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

<h2>Delete Return</h2>

<?php if ($errors): ?>
    <ul style="color:red;">
        <?php foreach ($errors as $err) echo "<li>$err</li>"; ?>
    </ul>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
    <a href="index.php">Back to Returns List</a>
<?php else: ?>
    <p>Are you sure you want to delete the following return?</p>

    <table>
        <tr><th>Return ID</th><td><?= htmlspecialchars($return['return_id']) ?></td></tr>
        <tr><th>Sale ID</th><td><?= htmlspecialchars($return['sale_id']) ?></td></tr>
        <tr><th>Product ID</th><td><?= htmlspecialchars($return['product_id']) ?></td></tr>
        <tr><th>Quantity</th><td><?= htmlspecialchars($return['quantity']) ?></td></tr>
        <tr><th>Reason</th><td><?= htmlspecialchars($return['reason']) ?></td></tr>
        <tr><th>Return Date</th><td><?= htmlspecialchars($return['return_date']) ?></td></tr>
    </table>

    <form method="post" style="margin-top:10px;">
        <button type="submit" style="color:white;background-color:red;">Confirm Delete</button>
        <a href="index.php?id=<?= $return_id ?>">Cancel</a>
    </form>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>
