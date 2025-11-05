<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

include '../../includes/header.php';

try {
    $stmt = $pdo->query("
        SELECT campaign_id, platform, start_date, end_date, budget, sales_generated, remarks
        FROM marketing
        ORDER BY start_date DESC
    ");
    $campaigns = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Failed to fetch marketing report: " . htmlspecialchars($e->getMessage()));
}
?>

<main class="main-content">
    <div class="card mb-sm">
        <h2>Marketing Report</h2>
    </div>

    <?php foreach ($campaigns as $c): 
        $roi = ($c['budget'] > 0) ? $c['sales_generated'] - $c['budget'] : 0;
    ?>
    <div class="card mb-md">
        <h3>Campaign No <?= (int)$c['campaign_id'] ?> : <?= htmlspecialchars($c['platform']) ?></h3>
        <p>Start: <?= htmlspecialchars($c['start_date']) ?> | End: <?= htmlspecialchars($c['end_date']) ?></p>
        <p>Budget: <?= CURRENCY ?> <?= number_format($c['budget'], 2) ?> | Sales Generated: <?= CURRENCY ?> <?= number_format($c['sales_generated'], 2) ?> | ROI: <?= CURRENCY ?> <?= number_format($roi, 2) ?></p>
        <p><strong>Remarks:</strong> <?= nl2br(htmlspecialchars($c['remarks'] ?? '-')) ?></p>
    </div>
    <?php endforeach; ?>

</main>

<?php include '../../includes/footer.php'; ?>
