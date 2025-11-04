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

?>

<?php include '../../includes/header.php'; ?>

<h2>Add New Customer</h2>

<?php if ($errors): ?>
    <ul style="color:red">
        <?php foreach($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?>
    </ul>
<?php endif; ?>

<form method="POST">
    <label>Name: <input type="text" name="name" required></label><br>
    <label>Phone: <input type="text" name="phone"></label><br>
    <label>Email: <input type="email" name="email"></label><br>
    <label>Region: <input type="text" name="region"></label><br>
    <label>Source: 
        <select name="source">
            <option value="walk-in">Walk-in</option>
            <option value="instagram">Instagram</option>
            <option value="whatsapp">WhatsApp</option>
            <option value="referral">Referral</option>
            <option value="other">Other</option>
        </select>
    </label><br>
    <button type="submit">Add Customer</button>
</form>

<?php include '../../includes/footer.php'; ?>
