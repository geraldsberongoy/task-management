<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header("Location: ../public/login.php");
    exit;
}
echo "<h2>Student Dashboard</h2>";
echo "<a href='../logout.php'>Logout</a>";
