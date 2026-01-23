<?php
require_once __DIR__ . '/auth.php';

$error = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if (login($_POST['password'])) {
        header('Location: /admin/dashboard.php');
        exit;
    } else {
        $error = 'Invalid password';
    }
}

// If already logged in, go to dashboard
if (is_logged_in()) {
    header('Location: /admin/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | All Saints</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', sans-serif;
      background: #f5f5f5;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .login-box {
      background: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }
    .login-box h1 {
      font-family: 'Oswald', sans-serif;
      font-size: 1.5rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 8px;
      color: #1a1f3c;
    }
    .login-box p {
      color: #666;
      margin-bottom: 24px;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-group label {
      display: block;
      font-weight: 500;
      margin-bottom: 8px;
      color: #333;
    }
    .form-group input {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 1rem;
      transition: border-color 0.2s;
    }
    .form-group input:focus {
      outline: none;
      border-color: #c9a227;
    }
    .btn {
      width: 100%;
      padding: 14px;
      background: #1a1f3c;
      color: white;
      border: none;
      border-radius: 8px;
      font-family: 'Oswald', sans-serif;
      font-size: 1rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      cursor: pointer;
      transition: background 0.2s;
    }
    .btn:hover { background: #2d2d4a; }
    .error {
      background: #fee;
      color: #c00;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    .brand {
      text-align: center;
      margin-bottom: 30px;
    }
    .brand span {
      color: #c9a227;
      font-size: 3rem;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <div class="brand">
      <span>&#9766;</span>
    </div>
    <h1>All Saints Admin</h1>
    <p>Enter your password to manage site content</p>

    <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autofocus>
      </div>
      <button type="submit" class="btn">Sign In</button>
    </form>
  </div>
</body>
</html>
