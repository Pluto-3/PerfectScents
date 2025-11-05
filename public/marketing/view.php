<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = (int)($_GET['id'] ?? 0);
$campaign = get_campaign_by_id($pdo, $id);

if (!$campaign) die("Campaign not found.");

include '../../includes/header.php';

// Calculate ROI
$roi = ($campaign['budget'] > 0)
    ? round((($campaign['sales_generated'] - $campaign['budget']) / $campaign['budget']) * 100, 2)
    : 0;
?>

<main class="main-content">

    <!-- Page Header -->
    <div class="card mb-sm">
        <h2>Marketing Campaign Details : <?= htmlspecialchars($campaign['platform']) ?></h2>
    </div>

    <!-- Campaign Details Table -->
    <div class="card">
        <table class="table">
            <tbody>
                <tr>
                    <th>Platform</th>
                    <td><?= htmlspecialchars($campaign['platform']) ?></td>
                </tr>
                <tr>
                    <th>Start Date</th>
                    <td><?= htmlspecialchars($campaign['start_date']) ?></td>
                </tr>
                <tr>
                    <th>End Date</th>
                    <td><?= htmlspecialchars($campaign['end_date']) ?></td>
                </tr>
                <tr>
                    <th>Budget</th>
                    <td><?= number_format($campaign['budget'], 2) ?></td>
                </tr>
                <tr>
                    <th>Sales Generated</th>
                    <td><?= number_format($campaign['sales_generated'], 2) ?></td>
                </tr>
                <tr>
                    <th>ROI</th>
                    <td><?= $roi ?>%</td>
                </tr>
                <tr>
                    <th>Remarks</th>
                    <td><?= nl2br(htmlspecialchars($campaign['remarks'])) ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Action Buttons -->
        <div class="flex gap-md mt-sm">
            <a href="edit.php?id=<?= $id ?>" class="btn btn-primary">Edit</a>
            <a href="delete.php?id=<?= $id ?>" class="btn btn-danger"
               onclick="return confirm('Are you sure you want to delete this campaign?');">
               Delete
            </a>
            <a href="index.php" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
