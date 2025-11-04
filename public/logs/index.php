<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$logs = get_logs($pdo);
?>

<?php include '../../includes/header.php'; ?>

<h2>User Logs / Audit Trail</h2>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>User</th>
        <th>Action</th>
        <th>Module</th>
        <th>Record ID</th>
        <th>Details</th>
        <th>Timestamp</th>
    </tr>
    <?php foreach($logs as $log): ?>
    <tr>
        <td><?= $log['log_id'] ?></td>
        <td><?= htmlspecialchars($log['username']) ?></td>
        <td><?= htmlspecialchars($log['action']) ?></td>
        <td><?= htmlspecialchars($log['module']) ?></td>
        <td><?= $log['record_id'] ?? '-' ?></td>
        <td><?= htmlspecialchars($log['details']) ?></td>
        <td><?= $log['created_at'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include '../../includes/footer.php'; ?>
