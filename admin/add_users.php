<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    die("Access denied.");
}

require_once "../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $role_id  = $_POST['role_id'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


    // OPTIONAL: check if email already exists
    $check = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        $message = "Email already exists.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword, $role_id]);
        $message = "User added successfully!";
    }
}
?>

<h3>Add New User</h3>
<?php if (isset($message)) echo "<p>$message</p>"; ?>
<form method="POST">
    <input name="name" placeholder="Full Name" required><br>
    <input name="email" type="email" placeholder="Email" required><br>
    <input name="password" placeholder="Password (plain)" required><br>
    <select name="role_id" required>
        <option value="2">Teacher</option>
        <option value="3">Student</option>
    </select><br>
    <button type="submit">Create User</button>
</form>
<a href="dashboard.php">← Back to Dashboard</a>