<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header("Location: ../public/login.php");
    exit;
}
echo "<h2>Admin Dashboard</h2>";
echo "<a href='add_users.php'>Manage Users</a><br>";
echo "<a href='../public/logout.php'>Logout</a>";
