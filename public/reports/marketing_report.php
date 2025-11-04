<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

?>

<?php include '../../includes/header.php'; ?>

<h2>Marketing Report</h2>

<?php
try {
    $stmt = $pdo->query("
        SELECT campaign_id, platform, start_date, end_date, budget, sales_generated, (sales_generated - budget) AS roi, remarks
        FROM marketing
        ORDER BY start_date DESC
    ");
    $campaigns = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Failed to fetch marketing report: " . htmlspecialchars($e->getMessage()));
}
?>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Campaign ID</th>
        <th>Platform</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Budget</th>
        <th>Sales Generated</th>
        <th>ROI</th>
        <th>Remarks</th>
    </tr>
    <?php foreach ($campaigns as $c): ?>
    <tr>
        <td><?= (int)$c['campaign_id'] ?></td>
        <td><?= htmlspecialchars($c['platform']) ?></td>
        <td><?= htmlspecialchars($c['start_date']) ?></td>
        <td><?= htmlspecialchars($c['end_date']) ?></td>
        <td><?= CURRENCY ?> <?= number_format($c['budget'], 2) ?></td>
        <td><?= CURRENCY ?> <?= number_format($c['sales_generated'], 2) ?></td>
        <td><?= CURRENCY ?> <?= number_format($c['roi'], 2) ?></td>
        <td><?= htmlspecialchars($c['remarks'] ?? '-') ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include '../../includes/footer.php'; ?>
