<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$settings = get_all_settings($pdo);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['setting_id'] as $i => $id) {
        $value = $_POST['value'][$i];
        try {
            update_setting($pdo, (int)$id, $value);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
    if (!$error) $success = "Settings updated successfully.";
    $settings = get_all_settings($pdo); // refresh
}
?>

<?php include '../../includes/header.php'; ?>

<h2>System Settings</h2>

<?php if($error): ?>
<p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php elseif($success): ?>
<p style="color:green"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="POST">
    <table border="1" cellpadding="5">
        <tr>
            <th>Name</th>
            <th>Value</th>
            <th>Description</th>
        </tr>
        <?php foreach($settings as $s): ?>
        <tr>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td>
                <input type="hidden" name="setting_id[]" value="<?= $s['setting_id'] ?>">
                <input type="text" name="value[]" value="<?= htmlspecialchars($s['value']) ?>">
            </td>
            <td><?= htmlspecialchars($s['description']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <button type="submit">Update Settings</button>
</form>

<?php include '../../includes/footer.php'; ?>
