<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header("Location: ../public/login.php");
    exit;
}
echo "<h2>Teacher Dashboard</h2>";
echo "You are logged in as: " . htmlspecialchars($_SESSION['user']['name']) . "<br>";
echo "<a>I am a teacher</a><br>";
echo "<a href='../logout.php'>Logout</a>";
