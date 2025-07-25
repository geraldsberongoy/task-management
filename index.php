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
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Task Management LMS</title>
  <style>
    :root {
      --primary-red: #880000;
      --secondary-red: #85144b;
      --primary-yellow: #FFDC00;
      --secondary-yellow: #FFB700;
      --light-yellow: #FFF6E5;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Arial', sans-serif;
      background: url('https://i.imgur.com/3D8NQAI.jpeg') center/cover no-repeat;
      color: #333;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      position: relative;
    }

    body::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.35);
      z-index: 0;
      pointer-events: none;
      filter: brightness(0.85);
    }

    main,
    header,
    footer {
      position: relative;
      z-index: 1;
    }

    header {
      background-color: var(--primary-red);
      color: white;
      padding: 40px 20px;
      text-align: center;
    }

    header h1 {
      font-size: 2.8rem;
      margin-bottom: 10px;
    }

    header p {
      font-size: 1.2rem;
    }

    main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
    }

    .main-flex {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 40px;
      flex-wrap: wrap;
    }


    .login-form {
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: #fff;
      padding: 32px 28px;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.07);
      min-width: 250px;
      max-width: 300px;
      align-items: center;
      gap: 20px;
    }

    .login-form h3 {
      margin-bottom: 8px;
      color: var(--secondary-red);
      font-size: 1.2rem;
      font-weight: 600;
    }

    .login-form input {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #eee;
      border-radius: 6px;
      font-size: 1rem;
      background: #fafafa;
      margin-bottom: 4px;
      outline: none;
      transition: border-color 0.2s;
    }

    .login-form input:focus {
      border-color: var(--primary-red);
    }

    .login-form button {
      width: 100%;
      padding: 10px 0;
      background: var(--primary-red);
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.2s;
    }

    .login-form button:hover {
      background: var(--secondary-red);
    }

    .pup-logo {
      width: 120px;
      height: auto;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      background: #fff;
      padding: 10px;
    }

    footer {
      background-color: var(--primary-red);
      color: white;
      text-align: center;
      padding: 15px 20px;
    }
  </style>
</head>

<body>

  <header>
    <h1>Task Management LMS</h1>
    <p>Empowering Students and Teachers to Manage Tasks Efficiently</p>
  </header>

  <main>
    <div class="main-flex">


      <form class="login-form" method="POST" autocomplete="off">
        <img src="https://www.pup.edu.ph/resources/images/logo.png" alt="PUP Logo" class="pup-logo" />
        <h3>Login</h3>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="email" name="email" placeholder="Email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" />
        <input type="password" name="password" type="password" required placeholder="Password" minlength="6"><br>
        <button type="submit">Login</button>
      </form>
    </div>
  </main>

  <footer>
    &copy; 2025 Task Management LMS. All Rights Reserved.
  </footer>

</body>

</html>