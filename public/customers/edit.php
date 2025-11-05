<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = (int)($_GET['id'] ?? 0);
$customer = get_customer_by_id($pdo, $id);
if (!$customer) die("Customer not found.");

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $region = trim($_POST['region'] ?? '');
    $source = trim($_POST['source'] ?? '');

    if (!$name) $errors[] = "Name is required.";

    if (empty($errors)) {
        update_customer($pdo, $id, compact('name','phone','email','region','source'));
        header('Location: view.php?id='.$id);
        exit();
    }
}

$sources = ['walk-in','instagram','whatsapp','referral','other'];

include '../../includes/header.php';
?>

<main class="main-content">

    <!-- Page Header -->
    <div class="card mb-sm">
        <h2>Edit Customer: <?= htmlspecialchars($customer['name']) ?></h2>
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

    <!-- Edit Form -->
    <div class="card">
        <form method="POST">
            <table class="table">
                <tr>
                    <th>Name <span style="color:red;">*</span></th>
                    <td><input type="text" name="name" value="<?= htmlspecialchars($customer['name']) ?>" required></td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td><input type="text" name="phone" value="<?= htmlspecialchars($customer['phone']) ?>"></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><input type="email" name="email" value="<?= htmlspecialchars($customer['email']) ?>"></td>
                </tr>
                <tr>
                    <th>Region</th>
                    <td><input type="text" name="region" value="<?= htmlspecialchars($customer['region']) ?>"></td>
                </tr>
                <tr>
                    <th>Source</th>
                    <td>
                        <select name="source">
                            <?php foreach ($sources as $s): ?>
                                <option value="<?= $s ?>" <?= $s === $customer['source'] ? 'selected' : '' ?>>
                                    <?= ucfirst($s) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>

            <div class="flex gap-md mt-sm">
                <button type="submit" class="btn btn-primary">Update Customer</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>
