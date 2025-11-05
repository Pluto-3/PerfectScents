<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = $_GET['id'] ?? null;
$campaign = get_campaign_by_id($pdo, $id);
if (!$campaign) die("Campaign not found.");

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        if (delete_campaign($pdo, $id)) {
            $success = "Campaign deleted successfully.";
        } else {
            $errors[] = "Failed to delete campaign.";
        }
    } else {
        header("Location: index.php");
        exit;
    }
}

include '../../includes/header.php';
?>

<main class="main-content">

    <div class="card mb-sm">
        <h2>Delete Campaign #<?= htmlspecialchars($campaign['campaign_id']) ?></h2>
    </div>

    <?php if ($errors): ?>
        <div class="card mb-sm" style="color:red;">
            <ul>
                <?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="card mb-sm" style="color:green;">
            <p><?= htmlspecialchars($success) ?></p>
            <a href="index.php" class="btn btn-secondary">Back to Campaigns</a>
        </div>
    <?php else: ?>
        <div class="card">
            <p>Are you sure you want to delete the campaign "<strong><?= htmlspecialchars($campaign['platform']) ?></strong>" that started on <?= htmlspecialchars($campaign['start_date']) ?>?</p>

            <form method="POST" class="flex gap-md mt-sm">
                <button type="submit" name="confirm" value="yes" class="btn btn-danger">Yes, Delete</button>
                <button type="submit" name="confirm" value="no" class="btn btn-secondary">Cancel</button>
            </form>
        </div>
    <?php endif; ?>

</main>

<?php include '../../includes/footer.php'; ?>
