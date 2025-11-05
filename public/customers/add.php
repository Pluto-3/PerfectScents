<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $region = trim($_POST['region'] ?? '');
    $source = trim($_POST['source'] ?? '');

    if (!$name) $errors[] = "Name is required.";

    if (empty($errors)) {
        add_customer($pdo, compact('name','phone','email','region','source'));
        header('Location: index.php');
        exit();
    }
}

$sources = ['--select option--','walk-in','instagram','whatsapp','referral','other'];

include '../../includes/header.php';
?>

<main class="main-content">

    <!-- Page Header -->
    <div class="card mb-sm">
        <h2>Add New Customer</h2>
    </div>

    <!-- Errors -->
    <?php if ($errors): ?>
        <div class="card mb-md" style="color:red;">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Add Form -->
    <div class="card">
        <form method="POST">
            <table class="table">
                <tr>
                    <th>Name <span style="color:red;">*</span></th>
                    <td><input type="text" name="name" value="<?= htmlspecialchars($name ?? '') ?>" required></td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td><input type="text" name="phone" value="<?= htmlspecialchars($phone ?? '') ?>"></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>"></td>
                </tr>
                <tr>
                    <th>Region</th>
                    <td><input type="text" name="region" value="<?= htmlspecialchars($region ?? '') ?>"></td>
                </tr>
                <tr>
                    <th>Source</th>
                    <td>
                        <select name="source">
                            <?php foreach ($sources as $s): ?>
                                <option value="<?= $s ?>" <?= (isset($source) && $s === $source) ? 'selected' : '' ?>>
                                    <?= ucfirst($s) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>

            <div class="flex gap-md mt-sm">
                <button type="submit" class="btn btn-primary">Add Customer</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
