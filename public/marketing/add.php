<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

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
        if (add_campaign($pdo, $data)) {
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Failed to add campaign.";
        }
    }
}

include '../../includes/header.php';
?>

<main class="main-content">

    <div class="card mb-sm">
        <h2>Add New Marketing Campaign</h2>
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
            <label>Platform name:</label>
            <input type="text" name="platform" value="<?= htmlspecialchars($_POST['platform'] ?? '') ?>" required>

            <label>Start Date:</label>
            <input type="date" name="start_date" value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>" required>

            <label>End Date:</label>
            <input type="date" name="end_date" value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>" required>

            <label>Budget (TZS):</label>
            <input type="number" name="budget" step="0.01" value="<?= htmlspecialchars($_POST['budget'] ?? 0) ?>" required>

            <label>Sales Generated (TZS):</label>
            <input type="number" name="sales_generated" step="0.01" value="<?= htmlspecialchars($_POST['sales_generated'] ?? 0) ?>" required>

            <label>Remarks:</label>
            <textarea name="remarks"><?= htmlspecialchars($_POST['remarks'] ?? '') ?></textarea>

            <div class="mt-sm">
                <button type="submit" class="btn btn-primary">Save Campaign</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
