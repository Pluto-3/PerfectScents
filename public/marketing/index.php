<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$campaigns = get_all_campaigns($pdo);
include '../../includes/header.php';
?>

<h2>Marketing Campaigns</h2>
<a href="add.php">Add New Campaign</a>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Platform</th>
        <th>Start</th>
        <th>End</th>
        <th>Budget</th>
        <th>Sales Generated</th>
        <th>ROI (%)</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($campaigns as $c): 
        $roi = ($c['budget'] > 0)
            ? round((($c['sales_generated'] - $c['budget']) / $c['budget']) * 100, 2)
            : 0;
    ?>
    <tr>
        <td><?= $c['campaign_id'] ?></td>
        <td><?= htmlspecialchars($c['platform']) ?></td>
        <td><?= htmlspecialchars($c['start_date']) ?></td>
        <td><?= htmlspecialchars($c['end_date']) ?></td>
        <td><?= number_format($c['budget'], 2) ?></td>
        <td><?= number_format($c['sales_generated'], 2) ?></td>
        <td><?= $roi ?>%</td>
        <td>
            <a href="view.php?id=<?= $c['campaign_id'] ?>">View</a> |
            <a href="edit.php?id=<?= $c['campaign_id'] ?>">Edit</a> |
            <a href="delete.php?id=<?= $c['campaign_id'] ?>" 
               onclick="return confirm('Delete this campaign?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include '../../includes/footer.php'; ?>
