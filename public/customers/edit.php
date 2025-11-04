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

?>

<?php include '../../includes/header.php'; ?>

<h2>Edit Customer</h2>

<?php if ($errors): ?>
    <ul style="color:red">
        <?php foreach($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?>
    </ul>
<?php endif; ?>

<form method="POST">
    <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($customer['name']) ?>" required></label><br>
    <label>Phone: <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone']) ?>"></label><br>
    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($customer['email']) ?>"></label><br>
    <label>Region: <input type="text" name="region" value="<?= htmlspecialchars($customer['region']) ?>"></label><br>
    <label>Source: 
        <select name="source">
            <?php
            $sources = ['walk-in','instagram','whatsapp','referral','other'];
            foreach ($sources as $s):
            ?>
            <option value="<?= $s ?>" <?= $s === $customer['source'] ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>
    </label><br>
    <button type="submit">Update Customer</button>
</form>

<?php include '../../includes/footer.php'; ?>
