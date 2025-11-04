<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

?>

<?php include '../../includes/header.php'; ?>

<h2>Low Stock Report</h2>

<?php
try {
    $stmt = $pdo->prepare("
        SELECT p.name, i.current_stock
        FROM inventory i
        JOIN products p ON i.product_id = p.product_id
        WHERE i.current_stock <= :threshold
        ORDER BY i.current_stock ASC
    ");
    $stmt->execute(['threshold' => STOCK_THRESHOLD]);
    $items = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Failed to fetch low stock report: " . htmlspecialchars($e->getMessage()));
}
?>

<?php if (!empty($items)): ?>
    <ul>
        <?php foreach ($items as $item): ?>
            <li><?= htmlspecialchars($item['name']) ?> - <?= (int)$item['current_stock'] ?> units</li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No low-stock items (all products above <?= STOCK_THRESHOLD ?> units).</p>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>
