<?php

require_once "../config/database.php";
require_once "../includes/auth.php";
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    die("Access denied.");
}
$stmt = $pdo->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show Users</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Users List</h1>
    <a href="add_users.php">Add New User</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role_id'] == 1 ? 'Admin' : ($user['role_id'] == 2 ? 'Teacher' : 'Student')); ?></td>
                <td>
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a> |
                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="dashboard.php">← Back to Dashboard</a>