<?php

require_once __DIR__ . '/../config/constants.php';
require_once 'session.php';
require_login();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= SITE_NAME ?></title>
<link rel="stylesheet" href="<?= ASSETS_URL ?>css/layout.css">
<link rel="stylesheet" href="<?= ASSETS_URL ?>css/theme.css">
<link rel="stylesheet" href="<?= ASSETS_URL ?>css/components.css">
<link rel="stylesheet" href="<?= ASSETS_URL ?>css/utilities.css">
</head>
<body>
<header>
    <div class="header-top">
        <h1><?= SITE_NAME ?></h1>
        <div class="user-info">
            Logged in as: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> |
            <a href="<?= BASE_URL ?>logout.php">Logout</a>
        </div>
    </div>
    <nav class="main-nav">
        <ul>
            <li><a href="<?= BASE_URL ?>dashboard.php">Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>suppliers/index.php">Suppliers</a></li>
            <li><a href="<?= BASE_URL ?>products/index.php">Products</a></li>
            <li><a href="<?= BASE_URL ?>purchases/index.php">Purchases</a></li>
            <li><a href="<?= BASE_URL ?>sales/index.php">Sales</a></li>
            <li><a href="<?= BASE_URL ?>returns/index.php">Returns</a></li>
            <li><a href="<?= BASE_URL ?>inventory/index.php">Inventory</a></li>
            <li><a href="<?= BASE_URL ?>customers/index.php">Customers</a></li>
            <li><a href="<?= BASE_URL ?>expenses/index.php">Expenses</a></li>
            <li><a href="<?= BASE_URL ?>marketing/index.php">Marketing</a></li>
            <li><a href="<?= BASE_URL ?>reports/index.php">Reports</a></li>
            <li><a href="<?= BASE_URL ?>settings/index.php">Settings</a></li>
            <li><a href="<?= BASE_URL ?>logs/index.php">Logs</a></li>
        </ul>
    </nav>
</header>
<main class="main-content">
