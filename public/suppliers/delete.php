<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

if (!isset($_GET['id'])) die("Supplier ID not specified.");

$supplier_id = (int)$_GET['id'];

try {
    delete_supplier($pdo, $supplier_id);
    header("Location: index.php");
    exit;
} catch (Exception $e) {
    die("Error deleting supplier: " . $e->getMessage());
}
?>
