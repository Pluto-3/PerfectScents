<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $supplier_id = $_POST['supplier_id'];
    $cost_price = $_POST['cost_price'];
    $retail_price = $_POST['retail_price'];
    $description = trim($_POST['description']);

    if ($name && $supplier_id && $cost_price && $retail_price) {
        if(add_product($pdo, $name, $supplier_id, $cost_price, $retail_price, $description)) {
            header('Location: index.php');
            exit();
        } else {
            $message = "Error adding product.";
        }
    } else {
        $message = "All fields are required.";
    }
}
?>

<?php include '../../includes/header.php'; ?>
<h2>Add Product</h2>
<?php if($message): ?>
    <p style="color:red;"><?= $message ?></p>
<?php endif; ?>
<form method="post">
    <label>Name:</label><input type="text" name="name" required><br>
    <label>Supplier ID:</label><input type="number" name="supplier_id" required><br>
    <label>Cost Price:</label><input type="number" step="0.01" name="cost_price" required><br>
    <label>Retail Price:</label><input type="number" step="0.01" name="retail_price" required><br>
    <label>Description:</label><textarea name="description"></textarea><br>
    <button type="submit">Add Product</button>
</form>
<?php include '../../includes/footer.php'; ?>
