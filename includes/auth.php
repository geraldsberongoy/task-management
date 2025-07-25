<?php
class Auth
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function validateLoginInput($email, $password)
    {
        $errors = [];

        if (empty($email) || empty($password)) {
            $errors[] = "Email and password are required.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }

        if (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters.";
        }

        return $errors;
    }

    public function authenticateUser($email, $password)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    public function createUserSession($user)
    {
        session_regenerate_id(true);
        $_SESSION['user'] = $user;
        $_SESSION['last_activity'] = time();
    }

    public function logout()
    {
        // Clear all session variables
        $_SESSION = [];

        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy the session
        session_destroy();

        // Redirect to login page
        header("Location: ../index.php");
        exit;
    }

    public function redirectBasedOnRole($role_id)
    {
        switch ($role_id) {
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
                throw new Exception("Unknown role.");
        }
        exit;
    }
}
