<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$campaigns = get_all_campaigns($pdo);

include '../../includes/header.php';
?>

<main class="main-content">

    <!-- Page Header -->
    <div class="card mb-sm" style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Marketing Campaigns</h2>
    </div>

    <div class="card mb-md flex end">
        <a href="add.php" class="btn btn-primary">Add New Campaign</a>
    </div>

    <!-- Campaigns Table -->
    <div class="card">
        <?php if (empty($campaigns)): ?>
            <p>No campaigns recorded yet.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Platform</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Budget (TZS)</th>
                        <th>Sales Generated (TZS)</th>
                        <th>ROI (%)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($campaigns as $c): 
                        $roi = ($c['budget'] > 0)
                            ? round((($c['sales_generated'] - $c['budget']) / $c['budget']) * 100, 2)
                            : 0;
                    ?>
                    <tr>
                        <td><?= (int)$c['campaign_id'] ?></td>
                        <td><?= htmlspecialchars(ucfirst($c['platform'])) ?></td>
                        <td><?= htmlspecialchars($c['start_date']) ?></td>
                        <td><?= htmlspecialchars($c['end_date']) ?></td>
                        <td><?= number_format($c['budget'], 2) ?></td>
                        <td><?= number_format($c['sales_generated'], 2) ?></td>
                        <td>
                            <strong class="<?= $roi >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= $roi ?>%
                            </strong>
                        </td>
                        <td>
                            <a href="view.php?id=<?= (int)$c['campaign_id'] ?>" class="btn btn-sm btn-secondary">View</a>
                            <a href="edit.php?id=<?= (int)$c['campaign_id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="delete.php?id=<?= (int)$c['campaign_id'] ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure you want to delete this campaign?');">
                               Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
