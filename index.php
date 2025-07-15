<?php
session_start();
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role_id'] == 1) {
        header("Location: admin/dashboard.php");
    }
    exit;
}
?>
<h2>Welcome</h2>
<a href="login.php">Login</a>