<?php
require_once 'session.php';

function require_role($role) {
    require_login();

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        // log unauthorized access attempt here SOON!
        die("Access denied. You do not have permission to view this page.");
    }
}

function require_roles(array $roles) {
    require_login();

    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles)) {
        die("Access denied. You do not have permission to view this page.");
    }
}
    