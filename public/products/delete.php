<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

if (!isset($_GET['id'])) die("Product ID missing.");
$product_id = (int)$_GET['id'];
$product = get_product_by_id($pdo, $product_id);
if (!$product) die("Product not found.");

try {
    delete_product($pdo, $product_id);
    header("Location: index.php?msg=Product+deleted+successfully");
    exit;
} catch (Exception $e) {
    die("Error deleting product: " . $e->getMessage());
}
