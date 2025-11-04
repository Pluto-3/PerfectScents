<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';
require_login();

$purchase_id = $_GET['id'] ?? null;
if (!$purchase_id) die("Purchase ID required.");

delete_purchase($pdo, $purchase_id);

header("Location: index.php");
exit;
