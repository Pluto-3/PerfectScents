<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'platform' => $_POST['platform'],
        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date'],
        'budget' => $_POST['budget'],
        'sales_generated' => $_POST['sales_generated'],
        'remarks' => $_POST['remarks']
    ];

    if (add_campaign($pdo, $data)) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Failed to add campaign.";
    }
}

include '../../includes/header.php';
?>

<h2>Add Marketing Campaign</h2>
<form method="POST">
    <label>Platform:</label><br>
    <input type="text" name="platform" required><br>

    <label>Start Date:</label><br>
    <input type="date" name="start_date" required><br>

    <label>End Date:</label><br>
    <input type="date" name="end_date" required><br>

    <label>Budget:</label><br>
    <input type="number" step="0.01" name="budget" required><br>

    <label>Sales Generated:</label><br>
    <input type="number" step="0.01" name="sales_generated" required><br>

    <label>Remarks:</label><br>
    <textarea name="remarks"></textarea><br><br>

    <button type="submit">Save</button>
</form>

<?php include '../../includes/footer.php'; ?>
