<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';
require_login();

$purchase_id = $_GET['id'] ?? null;

if (!$purchase_id || !is_numeric($purchase_id)) {
    die('Invalid purchase ID.');
}

try {
    $stmt = $pdo->prepare("DELETE FROM purchases WHERE purchase_id = :purchase_id");
    $stmt->execute([':purchase_id' => $purchase_id]);

    // Optional: log deletion
    log_action($pdo, $_SESSION['user_id'], 'delete', 'purchases', $purchase_id, 'Deleted purchase and associated items');

    header('Location: index.php');
    exit;
} catch (Exception $e) {
    echo "Error deleting purchase: " . htmlspecialchars($e->getMessage());
}
