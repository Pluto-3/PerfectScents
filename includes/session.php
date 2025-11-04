<?php

session_start();

// Timeout enforcer
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
}
$_SESSION['last_activity'] = time();

// Redirect if not logged in
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit();
    }
}

// Check user role
function require_role($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        die("Access denied. You do not have permission to view this page.");
    }
}
?>
