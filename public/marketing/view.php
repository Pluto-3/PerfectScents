<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = $_GET['id'] ?? null;
$campaign = get_campaign_by_id($pdo, $id);
if (!$campaign) die("Campaign not found");

include '../../includes/header.php';

$roi = ($campaign['budget'] > 0)
    ? round((($campaign['sales_generated'] - $campaign['budget']) / $campaign['budget']) * 100, 2)
    : 0;
?>

<h2>View Campaign</h2>
<p><strong>Platform:</strong> <?= htmlspecialchars($campaign['platform']) ?></p>
<p><strong>Start Date:</strong> <?= htmlspecialchars($campaign['start_date']) ?></p>
<p><strong>End Date:</strong> <?= htmlspecialchars($campaign['end_date']) ?></p>
<p><strong>Budget:</strong> <?= number_format($campaign['budget'], 2) ?></p>
<p><strong>Sales Generated:</strong> <?= number_format($campaign['sales_generated'], 2) ?></p>
<p><strong>ROI:</strong> <?= $roi ?>%</p>
<p><strong>Remarks:</strong> <?= nl2br(htmlspecialchars($campaign['remarks'])) ?></p>

<a href="edit.php?id=<?= $campaign['campaign_id'] ?>">Edit</a> |
<a href="index.php">Back</a>

<?php include '../../includes/footer.php'; ?>
