<?php
session_start();
require_once 'includes/config.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | EduCore</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    .message {
      max-width: 400px;
      margin: 2rem auto;
      padding: 1.5rem;
      border-radius: 8px;
      text-align: center;
    }
    .success {
      background: #e0f2f1;
      color: #00695c;
      border: 1px solid #00796b;
    }
    .error {
      background: #ffebee;
      color: #c62828;
      border: 1px solid #d32f2f;
    }
    a {
      display: block;
      text-align: center;
      margin-top: 1rem;
      color: #00796b;
      text-decoration: none;
    }
  </style>
</head>
<body>

  <h2>EduCore Login</h2>

  <form method="POST" action="">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="login">Login</button>
  </form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];

    echo '<div class="message success">✅ Login successful. Redirecting...</div>';
    echo '<script>setTimeout(() => { window.location.href = "dashboard.php"; }, 1500);</script>';
  } else {
    echo '<div class="message error">❌ Invalid email or password.</div>';
  }
}
?>

<a href="index.html">← Back to Register</a>

</body>
</html>
