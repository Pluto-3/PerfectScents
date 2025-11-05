<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

if (!isset($_GET['id'])) die("Supplier ID not specified.");

$supplier_id = (int)$_GET['id'];
$supplier = get_supplier_by_id($pdo, $supplier_id);
if (!$supplier) die("Supplier not found.");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?: '';
    $contact_person = $_POST['contact_person'] ?: null;
    $phone = $_POST['phone'] ?: null;
    $email = $_POST['email'] ?: null;
    $address = $_POST['address'] ?: null;
    $reliability_score = isset($_POST['reliability_score']) ? (float)$_POST['reliability_score'] : 0.0;

    if (empty($name)) {
        $error = "Supplier name is required.";
    } else {
        try {
            update_supplier($pdo, $supplier_id, $name, $contact_person, $phone, $email, $address, $reliability_score);
            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>

<?php include '../../includes/header.php'; ?>

<h2>Edit Supplier</h2>

<?php if ($error): ?>
<p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">
    <label>Name: <input type="text" name="name" required value="<?= htmlspecialchars($supplier['name']) ?>"></label><br>
    <label>Contact Person: <input type="text" name="contact_person" value="<?= htmlspecialchars($supplier['contact_person']) ?>"></label><br>
    <label>Phone: <input type="text" name="phone" value="<?= htmlspecialchars($supplier['phone']) ?>"></label><br>
    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($supplier['email']) ?>"></label><br>
    <label>Address: <textarea name="address"><?= htmlspecialchars($supplier['address']) ?></textarea></label><br>
    <label>Reliability Score: <input type="number" name="reliability_score" step="0.01" min="0" max="10" value="<?= number_format($supplier['reliability_score'], 2) ?>"></label><br>
    <button type="submit">Update Supplier</button>
</form>

<?php include '../../includes/footer.php'; ?>
