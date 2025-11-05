<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = (int)($_GET['id'] ?? 0);
$campaign = get_campaign_by_id($pdo, $id);
if (!$campaign) die("Campaign not found.");

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $platform = trim($_POST['platform'] ?? '');
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $budget = floatval($_POST['budget'] ?? 0);
    $sales_generated = floatval($_POST['sales_generated'] ?? 0);
    $remarks = trim($_POST['remarks'] ?? '');

    if (!$platform) $errors[] = "Platform is required.";
    if (!$start_date) $errors[] = "Start date is required.";
    if (!$end_date) $errors[] = "End date is required.";
    if ($budget < 0) $errors[] = "Budget cannot be negative.";
    if ($sales_generated < 0) $errors[] = "Sales generated cannot be negative.";

    if (empty($errors)) {
        $data = compact('platform', 'start_date', 'end_date', 'budget', 'sales_generated', 'remarks');
        if (update_campaign($pdo, $id, $data)) {
            header("Location: view.php?id=$id");
            exit;
        } else {
            $errors[] = "Failed to update campaign.";
        }
    }
}

include '../../includes/header.php';
?>

<main class="main-content">

    <div class="card mb-sm">
        <h2>Edit Campaign : <?= htmlspecialchars($campaign['platform']) ?></h2>
    </div>

    <?php if ($errors): ?>
        <div class="card mb-sm" style="color:red;">
            <ul>
                <?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" class="form">
            <label>Platform:</label>
            <input type="text" name="platform" value="<?= htmlspecialchars($campaign['platform']) ?>" required>

            <label>Start Date:</label>
            <input type="date" name="start_date" value="<?= htmlspecialchars($campaign['start_date']) ?>" required>

            <label>End Date:</label>
            <input type="date" name="end_date" value="<?= htmlspecialchars($campaign['end_date']) ?>" required>

            <label>Budget (TZS):</label>
            <input type="number" name="budget" step="0.01" value="<?= htmlspecialchars($campaign['budget']) ?>" required>

            <label>Sales Generated (TZS):</label>
            <input type="number" name="sales_generated" step="0.01" value="<?= htmlspecialchars($campaign['sales_generated']) ?>" required>

            <label>Remarks:</label>
            <textarea name="remarks"><?= htmlspecialchars($campaign['remarks']) ?></textarea>

            <div class="mt-sm">
                <button type="submit" class="btn btn-primary">Update Campaign</button>
                <a href="view.php?id=<?= $id ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
