<?php
session_start();
require_once '../includes/auth.php';
require_once '../config/database.php';

// Initialize Auth class
$auth = new Auth($pdo);

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['logout'])) {
    $auth->logout();
}

echo "<h2>Admin Dashboard</h2>";
echo "<a href='add_users.php'>Manage Users</a><br>";
echo "<a href='?logout=1'>Logout</a><br>";
echo "<a href='show_users.php'>View Users</a><br>";
