<?php
session_start();
require_once "config/database.php";
require_once "includes/auth.php";

// Initialize Auth class
$auth = new Auth($pdo);

// Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Validate CSRF token
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
            throw new Exception("CSRF token validation failed");
        }

        // Get and sanitize input
        $email = filter_var($_POST["email"] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST["password"] ?? '';

        // Validate input
        $errors = $auth->validateLoginInput($email, $password);
        
        if (empty($errors)) {
            // Authenticate user
            $user = $auth->authenticateUser($email, $password);
            
            if ($user) {
                // Create session and redirect
                $auth->createUserSession($user);
                $auth->redirectBasedOnRole($user['role_id']);
            } else {
                $error = "Invalid login credentials.";
            }
        } else {
            $error = implode('<br>', $errors);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <form method="POST" autocomplete="off">
        <h2>Login</h2>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input name="email" type="email" required placeholder="Email" 
               pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"><br>
        <input name="password" type="password" required placeholder="Password" 
               minlength="6"><br>
        <button type="submit">Login</button>
    </form>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
</body>
</html>