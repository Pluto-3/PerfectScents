<?php
require_once '../../config/constants.php';
require_once '../../includes/session.php';
require_once '../../includes/functions.php';

require_login();

$id = $_GET['id'] ?? null;
$campaign = get_campaign_by_id($pdo, $id);
if (!$campaign) die("Campaign not found");

if (delete_campaign($pdo, $id)) {
    header("Location: index.php");
    exit;
} else {
    echo "Failed to delete campaign.";
}
?>
