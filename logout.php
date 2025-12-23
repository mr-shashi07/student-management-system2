<?php
require_once 'config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$role = isset($_SESSION['admin_id']) ? 'admin' : 'student';

session_unset();
session_destroy();

redirect('login.php?role=' . $role . '&logout=1');
