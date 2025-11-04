<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = $_GET['id'] ?? null;
$inventory = get_inventory_by_id($pdo, $id);
if (!$inventory) die("Record not found");

if (delete_inventory_entry($pdo, $id)) {
    header("Location: index.php");
    exit;
} else {
    echo "Failed to delete record.";
}
?>
