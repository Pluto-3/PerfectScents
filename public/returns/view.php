<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$returns = get_all_returns($pdo);
?>

<?php include '../../includes/header.php'; ?>

<h2>Product Returns</h2>
<a href="add.php">Add New Return</a>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Return ID</th>
            <th>Sale ID</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Subtotal</th>
            <th>Reason</th>
            <th>Return Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($returns as $r): 
            // Get unit price from sale_items
            $stmt = $pdo->prepare("SELECT unit_price FROM sale_items WHERE sale_id = :sale_id AND product_id = :product_id");
            $stmt->execute(['sale_id' => $r['sale_id'], 'product_id' => $r['product_id']]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            $unit_price = $item ? $item['unit_price'] : 0;
            $subtotal = $unit_price * $r['quantity'];
        ?>
        <tr>
            <td><?= $r['return_id'] ?></td>
            <td><?= $r['sale_id'] ?></td>
            <td><?= htmlspecialchars($r['product_name']) ?></td>
            <td><?= $r['quantity'] ?></td>
            <td><?= number_format($unit_price, 2) ?></td>
            <td><?= number_format($subtotal, 2) ?></td>
            <td><?= htmlspecialchars($r['reason']) ?></td>
            <td><?= $r['return_date'] ?></td>
            <td>
                <a href="edit.php?id=<?= $r['return_id'] ?>">Edit</a> |
                <a href="delete.php?id=<?= $r['return_id'] ?>" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../../includes/footer.php'; ?>
