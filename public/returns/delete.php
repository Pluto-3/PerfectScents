<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$return_id = (int)$_GET['id'];

try {
    delete_return($pdo, $return_id);
    header("Location: index.php");
    exit();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
