<?php
require_once '../../includes/header.php';
require_once '../../includes/functions.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("DELETE FROM expenses WHERE expense_id = ?");
$stmt->execute([$id]);
header('Location: index.php');
exit;
