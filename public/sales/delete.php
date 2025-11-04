<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

if (!isset($_GET['id'])) die("Sale ID not specified.");
$sale_id = (int)$_GET['id'];

try {
    delete_sale($pdo, $sale_id);
    header("Location: index.php");
    exit;
} catch (Exception $e) {
    die("Error deleting sale: " . $e->getMessage());
}
