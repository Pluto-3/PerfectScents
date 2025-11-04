<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = (int)($_GET['id'] ?? 0);

if ($id && delete_customer($pdo, $id)) {
    header('Location: index.php');
    exit();
} else {
    die("Customer not found or could not be deleted.");
}
