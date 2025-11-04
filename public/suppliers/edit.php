<?php
require_once '../../includes/session.php';
require_login();
require_role('admin');
require_once '../../includes/functions.php';

$id = $_GET['id'] ?? null;
if (!$id) die("Supplier ID missing.");

$supplier = get_supplier($pdo, $id);
if (!$supplier) die("Supplier not found.");

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $contact_person = trim($_POST['contact_person']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $notes = trim($_POST['notes']);

    if(update_supplier($pdo, $id, $name, $contact_person, $phone, $email, $address, $notes)) {
        header('Location: index.php');
        exit();
    } else {
        $message = "Error updating supplier.";
    }
}
?>

<?php include '../../includes/header.php'; ?>

<h2>Edit Supplier</h2>
<?php if($message): ?>
    <p style="color:red;"><?= $message ?></p>
<?php endif; ?>

<form method="post">
    <label>Name:</label><input type="text" name="name" value="<?= htmlspecialchars($supplier['name']) ?>" required><br>
    <label>Contact Person:</label><input type="text" name="contact_person" value="<?= htmlspecialchars($supplier['contact_person']) ?>"><br>
    <label>Phone:</label><input type="text" name="phone" value="<?= htmlspecialchars($supplier['phone']) ?>" required><br>
    <label>Email:</label><input type="email" name="email" value="<?= htmlspecialchars($supplier['email']) ?>"><br>
    <label>Address:</label><input type="text" name="address" value="<?= htmlspecialchars($supplier['address']) ?>"><br>
    <label>Notes:</label><textarea name="notes"><?= htmlspecialchars($supplier['notes']) ?></textarea><br>
    <button type="submit">Update Supplier</button>
</form>

<?php include '../../includes/footer.php'; ?>
