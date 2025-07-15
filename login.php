<?php
session_start();
require_once "config/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION["user"] = $user;

        // Redirect by role
        switch ($user["role_id"]) {
            case 1:
                header("Location: admin/dashboard.php");
                break;
            case 2:
                header("Location: teacher/dashboard.php");
                break;
            case 3:
                header("Location: student/dashboard.php");
                break;
            default:
                echo "Unknown role.";
        }
        exit;
    } else {
        $error = "Invalid login credentials.";
    }
}
?>

<form method="POST">
    <h2>Login</h2>
    <input name="email" type="email" required placeholder="Email"><br>
    <input name="password" type="text" required placeholder="Password (plain)"><br>
    <button type="submit">Login</button>
</form>
<?php if (isset($error)) echo "<p>$error</p>"; ?>