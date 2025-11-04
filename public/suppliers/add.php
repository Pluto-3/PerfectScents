<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();
require_role('admin');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $contact_person = trim($_POST['contact_person']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $notes = trim($_POST['notes']);

    if ($name && $phone) {
        if(add_supplier($pdo, $name, $contact_person, $phone, $email, $address, $notes)) {
            header('Location: index.php');
            exit();
        } else {
            $message = "Error adding supplier.";
        }
    } else {
        $message = "Name and phone are required.";
    }
}
?>

<?php include '../../includes/header.php'; ?>

<h2>Add Supplier</h2>
<?php if($message): ?>
    <p style="color:red;"><?= $message ?></p>
<?php endif; ?>

<form method="post">
    <label>Name:</label><input type="text" name="name" required><br>
    <label>Contact Person:</label><input type="text" name="contact_person"><br>
    <label>Phone:</label><input type="text" name="phone" required><br>
    <label>Email:</label><input type="email" name="email"><br>
    <label>Address:</label><input type="text" name="address"><br>
    <label>Notes:</label><textarea name="notes"></textarea><br>
    <button type="submit">Add Supplier</button>
</form>

<?php include '../../includes/footer.php'; ?>
