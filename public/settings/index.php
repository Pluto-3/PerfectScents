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

include '../../includes/header.php';
?>

<main class="main-content">

    <div class="card mb-sm">
        <h2>System Settings</h2>
    </div>

    <?php if ($error): ?>
        <div class="card mb-sm" style="color:red;">
            <p><?= htmlspecialchars($error) ?></p>
        </div>
    <?php elseif ($success): ?>
        <div class="card mb-sm" style="color:green;">
            <p><?= htmlspecialchars($success) ?></p>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Value</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($settings as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['name']) ?></td>
                            <td>
                                <input type="hidden" name="setting_id[]" value="<?= $s['setting_id'] ?>">
                                <input type="text" name="value[]" value="<?= htmlspecialchars($s['value']) ?>">
                            </td>
                            <td><?= htmlspecialchars($s['description']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="mt-sm">
                <button type="submit" class="btn btn-primary">Update Settings</button>
            </div>
        </form>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
