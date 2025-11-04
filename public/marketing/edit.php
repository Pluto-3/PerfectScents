<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = $_GET['id'] ?? null;
$campaign = get_campaign_by_id($pdo, $id);
if (!$campaign) die("Campaign not found");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'platform' => $_POST['platform'],
        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date'],
        'budget' => $_POST['budget'],
        'sales_generated' => $_POST['sales_generated'],
        'remarks' => $_POST['remarks']
    ];

    if (update_campaign($pdo, $id, $data)) {
        header("Location: view.php?id=$id");
        exit;
    } else {
        $error = "Failed to update campaign.";
    }
}

include '../../includes/header.php';
?>

<h2>Edit Campaign</h2>
<form method="POST">
    <label>Platform:</label><br>
    <input type="text" name="platform" value="<?= htmlspecialchars($campaign['platform']) ?>" required><br>

    <label>Start Date:</label><br>
    <input type="date" name="start_date" value="<?= $campaign['start_date'] ?>" required><br>

    <label>End Date:</label><br>
    <input type="date" name="end_date" value="<?= $campaign['end_date'] ?>" required><br>

    <label>Budget:</label><br>
    <input type="number" step="0.01" name="budget" value="<?= $campaign['budget'] ?>" required><br>

    <label>Sales Generated:</label><br>
    <input type="number" step="0.01" name="sales_generated" value="<?= $campaign['sales_generated'] ?>" required><br>

    <label>Remarks:</label><br>
    <textarea name="remarks"><?= htmlspecialchars($campaign['remarks']) ?></textarea><br><br>

    <button type="submit">Update</button>
</form>

<?php include '../../includes/footer.php'; ?>
